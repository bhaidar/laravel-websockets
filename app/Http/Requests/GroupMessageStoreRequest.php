<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupMessageStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'min:5', 'max:255'],
        ];
    }
}
