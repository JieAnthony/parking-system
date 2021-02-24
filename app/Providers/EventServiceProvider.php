<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \Illuminate\Auth\Events\Login::class => [

        ],
        \Illuminate\Auth\Events\Registered::class => [

        ],
        \Illuminate\Auth\Events\Logout::class => [

        ],
        \App\Events\UserCarsEvent::class => [
            \App\Listeners\ClearUserCarCacheListener::class,
        ],
        \App\Events\FinancePaymentSuccessEvent::class => [
            \App\Listeners\SetCarUseDaysListener::class,
            \App\Listeners\NotificationUserBuySuccessListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
