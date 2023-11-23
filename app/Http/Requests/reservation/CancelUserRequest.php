<?php

namespace App\Http\Requests\reservation;

use Illuminate\Foundation\Http\FormRequest;

class CancelUserRequest extends FormRequest
{
    public function authorize(): bool
    {

        $reserveId = $this->route('reserveId');

        // 验证 $reserveId 是否是数字
        return is_numeric($reserveId);
    }

    public function rules(): array
    {
        return [];
    }

}
