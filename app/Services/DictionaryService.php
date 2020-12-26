<?php

namespace App\Services;

use App\Models\Dictionary;

class DictionaryService
{
    /**
     * @return array
     */
    public function getAdminSelect()
    {
        return Dictionary::query()->select(['id', 'name', 'order'])->orderBy('order')->pluck('name', 'id')->toArray();
    }
}
