<?php

namespace App\Listeners;

use App\Events\UserCarsEvent;
use Illuminate\Support\Facades\Cache;

class ClearUserCarCacheListener
{
    /**
     * @param UserCarsEvent $event
     */
    public function handle(UserCarsEvent $event)
    {
        Cache::forget('user_' . $event->user->id . '_cars');
    }
}
