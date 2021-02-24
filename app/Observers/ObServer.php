<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

class ObServer
{
    protected function forgetCache($key)
    {
        if (is_array($key)) {
            foreach ($key as $value) {
                Cache::forget($value);
            }
        }
        Cache::forget((string)$key);
    }
}
