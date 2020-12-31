<?php

namespace App\Http\Requests;

use App\Enums\PaymentModeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BuyLevelRequest extends FormRequest
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
        /** @var \App\Models\User $user */
        $user = $this->user();

        return [
            'car_id' => [
                'required',
                Rule::exists('cars', 'id'),
            ],
            'payment_mode' => [
                'required',
                Rule::in(PaymentModeEnum::getValues()),
            ],
        ];
    }
}
