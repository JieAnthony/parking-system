<?php

namespace App\Models;

class Level extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'price',
        'days',
        'note',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
