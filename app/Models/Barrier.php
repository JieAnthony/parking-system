<?php

namespace App\Models;

/**
 * App\Models\Barrier
 *
 * @property int $id
 * @property string $name
 * @property int $status
 * @property int $direction
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $enterOrder
 * @property-read int|null $enter_order_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $outOrder
 * @property-read int|null $out_order_count
 * @method static \Illuminate\Database\Eloquent\Builder|Model filter(array $input = [], $filter = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Barrier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model paginateFilter($perPage = null, $columns = [], $pageName = 'page', $page = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrier query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model simplePaginateFilter(?int $perPage = null, ?int $columns = [], ?int $pageName = 'page', ?int $page = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Model whereBeginsWith(string $column, string $value, string $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|Barrier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrier whereDirection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Model whereEndsWith(string $column, string $value, string $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|Barrier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Model whereLike(string $column, string $value, string $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|Barrier whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrier whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Barrier whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Barrier extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'status',
        'direction',
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
