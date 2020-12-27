<?php

namespace App\Services;

use App\Models\Dictionary;

class DictionaryService
{
    /**
     * @param bool $hasId
     * @return array
     */
    public function getAdminSelect(bool $hasId = true)
    {
        return Dictionary::query()
            ->select(['id', 'name', 'order'])
            ->orderBy('order')
            ->pluck('name', $hasId ? 'id' : 'name')
            ->toArray();
    }
}
