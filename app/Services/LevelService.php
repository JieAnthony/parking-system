<?php

namespace App\Services;

use App\Models\Level;
use Illuminate\Support\Arr;

class LevelService
{
    /**
     * @return array
     */
    public function getAdminSelect()
    {
        $levels = Level::query()->pluck('name', 'id')->toArray();

        return Arr::prepend($levels, '注册会员', 0);
    }
}
