<?php

namespace App\Events;

use App\Models\Car;
use App\Models\User;
use Illuminate\Queue\SerializesModels;

class UserCarsEvent
{
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * @var Car
     */
    public Car $car;

    public function __construct(User $user, Car $car)
    {
        $this->user = $user;
        $this->car = $car;
    }

}
