<?php

namespace App\Models;

class Profile extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'from',
        'open_id',
        'raw',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'raw' => 'json',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
