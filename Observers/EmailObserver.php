<?php

namespace App\Observers;

use App\Models\Email;
use App\Services\Mailcow\MailboxService;

class EmailObserver
{
    /**
     * Handle the Email "created" event.
     */
    public function created(Email $email): void
    {
        //
    }

    /**
     * Handle the Email "updated" event.
     */
    public function updated(Email $email): void
    {
        //
    }

    /**
     * Handle the Email "deleted" event.
     */
    public function deleted(Email $email): void
    {
        //
    }

    /**
     * Handle the Email "restored" event.
     */
    public function restored(Email $email): void
    {
        //
    }

    /**
     * Handle the Email "force deleted" event.
     */
    public function forceDeleted(Email $email): void
    {
        //
    }

    public function creating(Email $email)
    {
        $result = (new MailboxService())->create($email->person, $email->domain);
        if ($result === null){
            return false;
        }

        $email->username = $result->getUsername();
        $email->active = $result->getActive();
        $email->domain = $result->getDomain();
        $email->name = $result->getName();
        $email->quota = $result->getQuota();
        $email->local_part = $result->getLocalPart();
        $email->person_id = $result->getPersonId();

        return true;
    }
}
