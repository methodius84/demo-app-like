<?php

namespace App\Observers;

use App\Http\Controllers\Gitlab\UserController;
use App\Models\GitlabUser;
use Illuminate\Support\Facades\Session;

class GitlabUserObserver
{
    /**
     * Handle the GitlabUser "created" event.
     */
    public function created(GitlabUser $gitlabUser): void
    {
        //
    }

    /**
     * Handle the GitlabUser "updated" event.
     */
    public function updated(GitlabUser $gitlabUser): void
    {
        //
    }

    /**
     * Handle the GitlabUser "deleted" event.
     */
    public function deleted(GitlabUser $gitlabUser): void
    {
        //
    }

    /**
     * Handle the GitlabUser "restored" event.
     */
    public function restored(GitlabUser $gitlabUser): void
    {
        //
    }

    /**
     * Handle the GitlabUser "force deleted" event.
     */
    public function forceDeleted(GitlabUser $gitlabUser): void
    {
        //
    }

    public function creating(GitlabUser $gitlabUser)
    {
        $gitlabUser->email = $gitlabUser->person->email;
        $gitlabUser->username = explode('@',$gitlabUser->person->email)[0];
        $gitlabUser->name = $gitlabUser->person->last_name . ' ' .$gitlabUser->person->first_name;
        $response = resolve(UserController::class)->create($gitlabUser);

        if($response->getStatusCode() === 201){
            $data = json_decode($response->getBody()->getContents());
            \Log::channel('telegram')->info('test', [
                'id' => $data->id,
                'web_url' => $data->web_url,
                'is_bot' => $data->bot,
                'name' => $data->name,
                'created_at' => $data->created_at,
                'created_by' => $data->created_by->id ?? null,
            ]);
            $gitlabUser->id = $data->id;
            $gitlabUser->web_url = $data->web_url;
            $gitlabUser->is_bot = $data->bot;
            $gitlabUser->name = $data->name;
            $gitlabUser->created_at = $data->created_at;
            $gitlabUser->created_by = $data->created_by->id ?? null;
            Session::flash('success', 'User created');
            return true;
        }
        else{
            Session::flash('error', 'User was not created with code '.$response->getStatusCode());
            return false;
        }
    }

    public function updating(GitlabUser $gitlabUser): void
    {

    }

    public function deleting(GitlabUser $user)
    {
        $result = resolve(UserController::class)->delete($user);
        if($result === 204){
            Session::flash('success', 'User deleted');
            return true;
        }
        else{
            Session::flash('error', 'User wasnt deleted');
            return false;
        }
    }
}
