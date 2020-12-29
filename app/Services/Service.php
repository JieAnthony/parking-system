<?php

namespace App\Services;

abstract class Service
{
    /**
     * @return string
     */
    public function getNo()
    {
        return app('snowflake')->id();
    }
}
