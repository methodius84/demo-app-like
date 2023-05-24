<?php

namespace App\Observers;

use App\Models\bitrixAccount;

class BitrixAccountObserver
{
    /**
     * Handle the bitrixAccount "created" event.
     */
    public function created(bitrixAccount $bitrixAccount): void
    {
        //
    }

    /**
     * Handle the bitrixAccount "updated" event.
     */
    public function updated(bitrixAccount $bitrixAccount): void
    {
        //
    }

    /**
     * Handle the bitrixAccount "deleted" event.
     */
    public function deleted(bitrixAccount $bitrixAccount): void
    {
        //
    }

    /**
     * Handle the bitrixAccount "restored" event.
     */
    public function restored(bitrixAccount $bitrixAccount): void
    {
        //
    }

    /**
     * Handle the bitrixAccount "force deleted" event.
     */
    public function forceDeleted(bitrixAccount $bitrixAccount): void
    {
        //
    }

    public function creating(bitrixAccount $bitrixAccount): void
    {

    }

    public function updating(bitrixAccount $bitrixAccount): void
    {

    }
}
