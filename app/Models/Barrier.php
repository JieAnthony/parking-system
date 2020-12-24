<?php

namespace App\Models;

class Barrier extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'status',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enterOrder()
    {
        return $this->hasMany(Order::class, 'enter_barrier_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function outOrder()
    {
        return $this->hasMany(Order::class, 'out_barrier_id');
    }
}
