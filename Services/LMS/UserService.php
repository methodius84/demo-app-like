<?php

namespace App\Services\LMS;

use App\Models\LMSUser;
use App\Models\Person;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use function PHPUnit\Framework\isNull;

class UserService
{
    public function match(): void
    {
         $users = Person::query()->with('lmsUser')->get();

         foreach ($users as $user){
             if (is_null($user->lmsUser)){
                 try {
                     $lmsUser = LMSUser::whereEmail($user->email)->firstOrFail();
                     $lmsUser->person()->associate($user);
                     print_r('User '.$user->email.' associated with lms user'.PHP_EOL);
                 }
                 catch (ModelNotFoundException){
                     print_r('User '.$user->email.' not found on LMS'.PHP_EOL);
                 }
             }
         }
    }

    public function create(array $payload)
    {
        try {
            $user = Person::whereEmail($payload['email'])->firstOrFail();
            $user->lmsUser->updateQuietly([
                'platform_id' => $payload['id'],
            ]);
        }
        catch (ModelNotFoundException $exception){
            \Log::channel('telegram')->error('Model not found for user '.$payload['email'].', retry this job', $payload);
        }
    }

    public function update($payload)
    {
        try {
            $user = LMSUser::whereEmail($payload['data']['email'])->firstOrFail();
            $user->update([
                'platform_id' => $payload['id'],
            ]);
        }
        catch (ModelNotFoundException){
            \Log::channel('telegram')->error('Model not found', $payload);
        }
    }

    public function info($payload)
    {
        $user = Person::whereEmail($payload['user']['email'])->firstOrFail()->lmsUser;


        $user->updateQuietly([
            'platform_id' => $payload['user']['id'],
            'roles' => $payload['user']['roles'],
        ]);
        \Log::channel('telegram')->info('lms user info updated', ['id' => $user->id]);
    }
}
