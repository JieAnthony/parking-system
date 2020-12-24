<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserHasCar extends Pivot
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'car_id',
    ];
}
