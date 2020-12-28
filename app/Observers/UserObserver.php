<?php

namespace App\Observers;

use App\Models\User;

class UserObserver extends ObServer
{
    /**
     * Handle the User "updated" event.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function updated(User $user)
    {
        $this->forgetCache('user_'.$user->id);
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function deleted(User $user)
    {
        $this->forgetCache('user_'.$user->id);
    }

    /**
     * Handle the User "restored" event.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function restored(User $user)
    {
        $this->forgetCache('user_'.$user->id);
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        $this->forgetCache('user_'.$user->id);
    }
}
