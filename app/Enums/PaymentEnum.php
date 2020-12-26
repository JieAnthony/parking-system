<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static CASH()
 * @method static static WECHAT()
 * @method static static ALI()
 */
final class PaymentEnum extends Enum
{
    const CASH = 0;
    const WECHAT = 1;
    const ALI = 2;

    public static function getDescription($value): string
    {
        switch ($value) {
            case self::CASH:
                return '现金';
            case self::WECHAT:
                return '微信支付';
            case self::ALI:
                return '支付宝支付';
            default:
                return self::getDescription($value);
        }
    }
}
