<?php

namespace App\Providers;

use App\Models\AmoUser;
use App\Models\Email;
use App\Models\GitlabUser;
use App\Models\LMSUser;
use App\Models\Person;
use App\Observers\AmoUserObserver;
use App\Observers\EmailObserver;
use App\Observers\GitlabUserObserver;
use App\Observers\LMSUserObserver;
use App\Observers\PersonObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        LMSUser::observe(LMSUserObserver::class);
        GitlabUser::observe(GitlabUserObserver::class);
        Person::observe(PersonObserver::class);
        AmoUser::observe(AmoUserObserver::class);
        Email::observe(EmailObserver::class);
    }
}
