<?php

namespace App\Models;

/**
 * App\Models\Qa
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Model filter(array $input = [], $filter = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Qa newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Qa newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model paginateFilter($perPage = null, $columns = [], $pageName = 'page', $page = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Qa query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model simplePaginateFilter(?int $perPage = null, ?int $columns = [], ?int $pageName = 'page', ?int $page = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Model whereBeginsWith(string $column, string $value, string $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|Qa whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Qa whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Model whereEndsWith(string $column, string $value, string $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|Qa whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Model whereLike(string $column, string $value, string $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|Qa whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Qa whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Qa extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'title',
        'content',
    ];

    /**
     * @param $value
     * @return string
     */
    public function getContentAttribute($value)
    {
        return htmlspecialchars_decode($value);
    }
}
