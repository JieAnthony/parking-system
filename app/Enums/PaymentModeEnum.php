<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static CASH()
 * @method static static WECHAT()
 * @method static static ALI()
 */
final class PaymentModeEnum extends Enum
{
    const LEVEL_CAR = 1;
    const FREE_TIME = 2;
    const CASH = 3;
    const WECHAT = 4;

    /**
     * @param mixed $value
     * @return string
     */
    public static function getDescription($value) : string
    {
        switch ($value) {
            case self::LEVEL_CAR:
                return '月卡车辆';
            case self::FREE_TIME:
                return '免费时间';
            case self::CASH:
                return '现金支付';
            case self::WECHAT:
                return '微信支付';
            default:
                return self::getDescription($value);
        }
    }
}
