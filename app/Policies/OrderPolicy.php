<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @param Order $order
     * @return bool
     */
    public function own(User $user, Order $order) : bool
    {
        return $user->id == $order->user_id;
    }
}
