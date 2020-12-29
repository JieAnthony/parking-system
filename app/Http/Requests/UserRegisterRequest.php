<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
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
            'username' => 'required|unique:users|phone',
            'password' => 'required|string|min:6|max:32|confirmed',
            'code' => 'required|string|digits:'.config('info.sms_length'),
            'nickname' => 'required|string|max:255',
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
            'nickname' => '昵称',
        ];
    }
}
