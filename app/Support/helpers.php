<?php

use Illuminate\Support\Facades\Cache;

if (! function_exists('getOption')) {
    /**
     * @param string $key
     * @return array|mixed
     */
    function getOption(string $key)
    {
        $data = Cache::get($key);
        if (! $data) {
            $data = \Option::get($key);
            if ($data) {
                Cache::forever($key, $data);
            }
        }

        return $data;
    }
}
