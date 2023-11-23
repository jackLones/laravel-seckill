<?php

namespace App\Http\Requests\order;

use Illuminate\Foundation\Http\FormRequest;

class SubmitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|numeric',
            'buy_num' => 'required|numeric',
//            'reserve_start_time' => 'required|date',
//            'reserve_end_time' => 'required|date',
//            'seckill_start_time' => 'required|date',
//            'seckill_end_time' => 'required|date',
//            'creator' => 'required|string',
        ];
    }
}
