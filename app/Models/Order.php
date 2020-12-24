<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    /**
     * @var string[]
     */
    protected $fillable = [
        'no',
        'user_id',
        'car_id',
        'status',
        'enter_barrier_id',
        'out_barrier_id',
        'payment',
        'price',
        'outed_at',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'outed_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function enterBarrier()
    {
        return $this->belongsTo(Barrier::class, 'enter_barrier_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function outBarrier()
    {
        return $this->belongsTo(Barrier::class, 'out_barrier_id');
    }
}
