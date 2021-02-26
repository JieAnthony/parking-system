<?php

namespace App\Providers;

use App\Events\CarEnterEvent;
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
        ],
        \App\Events\CarEnterEvent::class => [
            \App\Listeners\GenerateOrderListener::class
        ],
        \App\Events\OrderCreateEvent::class => [
        ],
        \App\Events\OrderPaymentSuccessEvent::class => [
        ],
        \App\Events\OrderCarLeaveEvent::class => [
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
