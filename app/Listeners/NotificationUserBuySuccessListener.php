<?php

namespace App\Listeners;

use App\Events\FinancePaymentSuccessEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificationUserBuySuccessListener implements ShouldQueue
{
    public function handle(FinancePaymentSuccessEvent $event)
    {
        $user = $event->finance->user;
        \Log::info('send level payment success message to user', [
            'user' => $user->id
        ]);
    }
}
