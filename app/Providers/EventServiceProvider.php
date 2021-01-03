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
        \App\Events\Finance\FinancePaySuccessEvent::class => [

        ],
        \App\Events\Order\OrderCreatedEvent::class => [

        ],
        \App\Events\Order\OrderPaySuccessEvent::class => [

        ],
        \App\Events\Order\OrderCarLeaveEvent::class => [

        ],
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
