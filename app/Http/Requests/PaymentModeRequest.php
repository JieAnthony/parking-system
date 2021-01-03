<?php

namespace App\Http\Requests;

use App\Enums\PaymentModeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentModeRequest extends FormRequest
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
            'payment_mode' => [
                'required',
                Rule::in(PaymentModeEnum::getValues()),
            ],
        ];
    }
}
