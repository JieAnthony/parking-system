<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserFromEnum extends Enum
{
    const PROGRAM = 1;

    /**
     * @param mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        switch ($value) {
            case self::PROGRAM:
                return '微信小程序';
            default:
                return self::getDescription($value);
        }
    }
}
