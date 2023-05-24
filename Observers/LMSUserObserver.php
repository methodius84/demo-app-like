<?php

namespace App\Observers;

use App\DTO\LMS\CreateUserDTO;
use App\DTO\LMS\UpdateUserDTO;
use App\DTO\LMS\UserDTO;
use App\Models\LMSUser;

class LMSUserObserver
{
    /**
     * Handle the LMSUser "created" event.
     */
    public function created(LMSUser $user): void
    {
        //
    }

    /**
     * Handle the LMSUser "updated" event.
     */
    public function updated(LMSUser $user): void
    {
        //
    }

    /**
     * Handle the LMSUser "deleted" event.
     */
    public function deleted(LMSUser $user): void
    {
        //TODO : api DELETE method(await LMS api route)
    }

    /**
     * Handle the LMSUser "restored" event.
     */
    public function restored(LMSUser $user): void
    {
        //
    }

    /**
     * Handle the LMSUser "force deleted" event.
     */
    public function forceDeleted(LMSUser $user): void
    {
        //
    }

    public function creating(LMSUser $user): void
    {
        $dto = new UserDTO($user);

        \Queue::connection('rabbitmq')->pushRaw(json_encode((new CreateUserDTO($dto))->toArray()), 'likecc_in');
    }

    public function updating(LMSUser $user): void
    {
        $dto = new UserDTO($user);

        \Queue::connection('rabbitmq')->pushRaw(json_encode((new UpdateUserDTO($dto))->toArray()), 'likecc_in');
    }
}
