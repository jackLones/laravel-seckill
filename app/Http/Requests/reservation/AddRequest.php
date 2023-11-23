<?php

namespace App\Http\Requests\reservation;

use Illuminate\Foundation\Http\FormRequest;

class AddRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku_id' => 'required|numeric',
            'reserve_start_time' => 'required|date',
            'reserve_end_time' => 'required|date',
            'seckill_start_time' => 'required|date',
            'seckill_end_time' => 'required|date',
            'creator' => 'required|string',
        ];
    }
}
