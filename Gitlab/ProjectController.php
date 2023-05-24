<?php

namespace App\Http\Controllers\Gitlab;

use App\Http\Controllers\Controller;
use App\Models\GitlabGroup;
use App\Models\GitlabProject;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function sync(){
        $client = new Client(['base_uri' => config('services.gitlab.base_uri'), 'headers' => [
            'Authorization' => 'Bearer '.config('services.gitlab.token'),
        ]
        ]);

        try{
            $response = $client->get('api/v4/projects');

            return match ($response->getStatusCode()){
                404 => redirect()->back()->with(['errors' => 'Not found']),
                200 => $this->upsertProjects(json_decode($response->getBody()), $client),
            };
        }
        catch (GuzzleException $exception){
            return redirect()->back()->withErrors($exception);
        }
    }

    public function upsertProjects(array $projects, Client $client){
        foreach ($projects as $project){
            GitlabProject::updateOrCreate(
                [
                    'id' => $project->id,
                ],
                [
                    'name' => $project->name,
                    'description' => $project->description,
                    'visibility' => $project->visibility,
                    'web_url' => $project->web_url,
                    'created_at' => $project->created_at,
                    'creator_id' => $project->creator_id,
                    'group_id' => $project->namespace->id ?? null,
                ]
            );

            $this->setProjectUserRelation($project->id, $client);

        }

        return redirect()->back()->with(['status' => 'success']);
    }

    private function setProjectUserRelation(int $project_id, Client $client)
    {
        try {
            $response = $client->get('api/v4/projects/'.$project_id.'/users');
            $users = json_decode($response->getBody());

            DB::table('gitlab_users_projects')->where('project_id', $project_id)->delete();
            foreach ($users as $user)
            {
                DB::table('gitlab_users_projects')->insert([
                    'user_id' => $user->id,
                    'project_id' => $project_id,
                ]);
            }
        }
        catch (GuzzleException $exception){
            \Log::channel('telegram')->error('Error getting gitlab users for project '.$project_id, [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ]);
        }
    }

    // ТЕСТОВЫЙ ЗАПРОС В ГИТ
    public function getUsers(){
        $url = config('services.gitlab.base_uri');

        $client = new Client(['base_uri' => $url, 'headers' => [
            'Authorization' => 'Bearer '.config('services.gitlab.token'),
        ]]);

        $response = $client->get('api/v4/projects/17/users', [
            'scopes' => config('services.gitlab.scopes')
        ]);
        dd(json_decode($response->getBody()));
    }
}
