<?php

namespace App\Models;

class Car extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'first_word',
        'license',
        'is_big',
        'status',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'status' => 'boolean',
        'is_big' => 'boolean',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_has_cars',
            'car_id',
            'user_id'
        )->withTimestamps()->using(UserHasCar::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
