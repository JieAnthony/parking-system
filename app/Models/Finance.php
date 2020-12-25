<?php

namespace App\Models;

/**
 * App\Models\Finance.
 *
 * @property int $id
 * @property string $no
 * @property int $user_id
 * @property int $level_id
 * @property int|null $payment
 * @property mixed|null $price
 * @property bool $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Level $level
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Model filter(array $input = [], $filter = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Finance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Finance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model paginateFilter($perPage = null, $columns = [], $pageName = 'page', $page = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Finance query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model simplePaginateFilter(?int $perPage = null, ?int $columns = [], ?int $pageName = 'page', ?int $page = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Model whereBeginsWith(string $column, string $value, string $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|Finance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Model whereEndsWith(string $column, string $value, string $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|Finance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Finance whereLevelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Model whereLike(string $column, string $value, string $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|Finance whereNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Finance wherePayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Finance wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Finance whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Finance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Finance whereUserId($value)
 * @mixin \Eloquent
 */
class Finance extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'no',
        'user_id',
        'level_id',
        'price',
        'payment',
        'status',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'status' => 'boolean',
        'price' => 'decimal:2',
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
    public function level()
    {
        return $this->belongsTo(Level::class);
    }
}
