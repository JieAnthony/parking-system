<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static PARKING()
 * @method static static DONE()
 */
final class OrderStatusEnum extends Enum
{
    const PARKING = 0;
    const DONE = 1;

    /**
     * @param mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        switch ($value) {
            case self::PARKING:
                return '停车中';
            case self::DONE:
                return '已完成';
            default:
                return self::getDescription($value);
        }
    }
}
