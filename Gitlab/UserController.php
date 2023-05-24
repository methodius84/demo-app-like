<?php

namespace App\Http\Controllers\Gitlab;

use App\Http\Controllers\Controller;
use App\Models\GitlabUser;
use App\Models\Person;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Laravel\Nova\Actions\Action;

class UserController extends Controller
{
    public function sync(){
        $client = new Client(['base_uri' => config('services.gitlab.base_uri'), 'headers' => [
            'Authorization' => 'Bearer '.config('services.gitlab.token'),
        ]
        ]);

        try{
            $response = $client->get('api/v4/users');

            return match ($response->getStatusCode()){
                404 => redirect()->back()->with(['errors' => 'Resource not found']),
                200 => $this->upsertUsers(json_decode($response->getBody()->getContents())),
            };
        }
        catch (GuzzleException $exception){
            return redirect()->back()->with(['status' => $exception->getMessage()]);
        }
    }

    public function upsertUsers(array $users){
        foreach ($users as $user){
            GitlabUser::withoutEvents(function () use ($user){
                GitlabUser::updateOrCreate(
                    [
                        'email' => $user->email,
                    ],
                    [
                        'id' => $user->id,
                        'username' => $user->username,
                        'name' => $user->name,
                        'state' => $user->state,
                        'is_bot' => $user->bot,
                        'web_url' => $user->web_url,
                        'created_at' => $user->created_at,
                        'last_sign_in_at' => $user->last_sign_in_at,
                        'last_activity_on' => $user->last_activity_on,
                        'current_sign_in' => $user->current_sign_in_at,
                        'is_admin' => $user->is_admin,
                        'created_by' => $user->created_by->id ?? null,
                        'person_id' => Person::whereEmail($user->email)->first()->id ?? null
                    ]
                );
            });
        }

        return redirect()->back()->with(['status' => 'success']);
    }

    public function create(GitlabUser $user)
    {
        $client = new Client(['base_uri' => config('services.gitlab.base_uri'), 'headers' => [
            'Authorization' => 'Bearer '.config('services.gitlab.token'),
        ]
        ]);

        $body = [
            'email' => $user->person->email,
            'username' => explode('@', $user->person->email)[0],
            'name' => $user->person->last_name . ' ' .$user->person->first_name,
            'force_random_password' => true,
        ];
        return $client->post('api/v4/users', [
            'json' => $body,
        ]);
    }

    public function delete(GitlabUser $user)
    {
        $client = new Client(['base_uri' => config('services.gitlab.base_uri'), 'headers' => [
            'Authorization' => 'Bearer '.config('services.gitlab.token'),
        ]
        ]);

        $response = $client->delete('api/v4/users/'.$user->id);

        return $response->getStatusCode();
    }
}
