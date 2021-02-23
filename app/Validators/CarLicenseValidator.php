<?php


namespace App\Validators;


class CarLicenseValidator
{
    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     * @return false|int
     */
    public function validate($attribute, $value, $parameters, $validator)
    {
        return preg_match('/[京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新使]{1}[A-Z]{1}[0-9a-zA-Z]{5}$/u', $value);
    }
}