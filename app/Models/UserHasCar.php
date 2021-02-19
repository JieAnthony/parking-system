<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\UserHasCar
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UserHasCar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserHasCar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserHasCar query()
 * @mixin \Eloquent
 */
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
