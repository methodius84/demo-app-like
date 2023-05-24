<?php

namespace App\Http\Controllers\Gitlab;

use App\Http\Controllers\Controller;
use App\Models\GitlabGroup;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function sync(){
        $client = new Client(['base_uri' => config('services.gitlab.base_uri'), 'headers' => [
            'Authorization' => 'Bearer '.config('services.gitlab.token'),
        ]
        ]);

        try{
            $response = $client->get('api/v4/groups');

            return match ($response->getStatusCode()){
                404 => redirect()->back()->with(['errors' => 'Not found']),
                200 => $this->upsertGroups(json_decode($response->getBody()), false),
            };
        }
        catch (GuzzleException $exception){
            return redirect()->back()->withErrors($exception);
        }
    }

    public function upsertGroups(array $groups){
        foreach ($groups as $group){
            GitlabGroup::updateOrCreate(
                [
                    'id' => $group->id,
                ],
                [
                    'name' => $group->name,
                    'web_url' => $group->web_url,
                    'created_at' => $group->created_at,
                    'parent_id' => $group->parent_id ?? null
                ]
            );
        }

        return redirect()->back()->with(['status' => 'success']);
    }
}
