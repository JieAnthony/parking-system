<?php

namespace App\Services;

abstract class Service
{
    /**
     * @param bool $isOrder
     * @return string
     */
    public function getNo(bool $isOrder = true)
    {
        $no = app('snowflake')->id();

        return $isOrder ? 'P'.$no : 'F'.$no;
    }
}
