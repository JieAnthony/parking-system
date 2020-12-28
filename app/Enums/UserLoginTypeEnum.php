<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class UserLoginTypeEnum extends Enum
{
    const PASSWORD = 1;
    const CODE = 2;
    const MINI_PROGRAM = 3;

    /**
     * @param mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        switch ($value) {
            case self::PASSWORD:
                return '账号密码登录';
            case self::CODE:
                return '短信验证码';
            case self::MINI_PROGRAM:
                return '小程序登录';
            default:
                return self::getDescription($value);
        }
    }
}
