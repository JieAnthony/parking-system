<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OK()
 * @method static static FAIL()
 */
final class FinanceEnum extends Enum
{
    const OK = 1;
    const FAIL = 0;

    /**
     * @param mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        switch ($value) {
            case self::OK:
                return '交易成功';
            case self::FAIL:
                return '交易失败';
            default:
                return self::getDescription($value);
        }
    }
}
