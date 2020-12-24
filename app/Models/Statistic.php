<?php

namespace App\Models;

class Statistic extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'created_user_total',
        'collect_money_total',
        'record_date',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'record_date' => 'date',
    ];
}
