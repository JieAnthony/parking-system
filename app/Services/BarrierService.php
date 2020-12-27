<?php

namespace App\Services;

use App\Models\Barrier;

class BarrierService
{
    /**
     * @param bool $direction
     * @return array
     */
    public function getAdminSelect(bool $direction = true)
    {
        return Barrier::query()
            ->where('direction', $direction)
            ->orderByDesc('id')
            ->pluck('name', 'id')
            ->toArray();
    }
}
