<?php

namespace App\Http\Requests\reservation;

use Illuminate\Foundation\Http\FormRequest;


class AddUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku_id' => 'required|numeric',
            'user_id' => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'sku_id.required' => 'sku_id不能为空',
            'sku_id.numeric' => 'sku_id必须是整数',
            'user_id.required' => 'user_id不能为空',
            'user_id.integer' => 'user_id必须是整数',
        ];
    }

}
