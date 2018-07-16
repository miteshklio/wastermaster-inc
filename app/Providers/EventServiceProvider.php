<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\RequestBidsForLead' => [
            'App\Listeners\EmailBidRequests',
        ],
        'App\Events\PreBidMatchRequest' => [
            'App\Listeners\EmailPreMatchRequest',
        ],
        'App\Events\PostBidMatchRequest' => [
            'App\Listeners\EmailPostMatchRequest',
        ],
        'App\Events\AcceptedBid' => [
            'App\Listeners\EmailAcceptedBidder'
        ],
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\LogSuccessfulLogin',
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
