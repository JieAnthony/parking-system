<?php

namespace App\Http\Requests;

use App\Enums\UserLoginTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => [
                'required',
                Rule::in(UserLoginTypeEnum::getValues()),
            ],
            'username' => [
                'required_if:type,'.UserLoginTypeEnum::PASSWORD.','.UserLoginTypeEnum::CODE,
                'regex:/^1[35678][0-9]{9}$/',
                Rule::exists('users', 'username'),
            ],
            //密码登录
            'password' => 'required_if:type,'.UserLoginTypeEnum::PASSWORD.'|string|min:6|max:32',
            //验证码登录
            'code' => 'required_if:type,'.UserLoginTypeEnum::CODE.'|string|digits:6',
            //小程序登录
            'js_code' => 'required_if:type,'.UserLoginTypeEnum::MINI_PROGRAM.'|string',
            'iv' => 'required_if:type,'.UserLoginTypeEnum::MINI_PROGRAM.'|string',
            'encryptedData' => 'required_if:type,'.UserLoginTypeEnum::MINI_PROGRAM.'|string',
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [
            'password.required_if' => '当 登陆类型 为 账号密码登录 时 密码 不能为空。',
            'code.required_if' => '当 登陆类型 为 验证码登录 时 验证码 不能为空。',
            'js_code.required_if' => '当 登陆类型 为 小程序登录 时 js_code 不能为空。',
            'iv.required_if' => '当 登陆类型 为 小程序登录 时 iv 不能为空。',
            'encryptedData.required_if' => '当 登陆类型 为 小程序登录 时 encryptedData 不能为空。',
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'username' => '手机号',
            'password' => '密码',
            'code' => '验证码',
            'type' => '登陆类型',
        ];
    }
}
