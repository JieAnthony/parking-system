<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static ENTER()
 * @method static static OUT()
 */
final class BarrierDirectionEnum extends Enum
{
    const ENTER = 1;
    const OUT = 0;

    /**
     * @param mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        switch ($value) {
            case self::ENTER:
                return '进';
            case self::OUT:
                return '出';
            default:
                return self::getDescription($value);
        }
    }
}
