<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLectureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'topic' => [
                'sometimes',
                'string',
                'max:500',
                Rule::unique('lectures')->ignore($this->route('lecture'))
            ],
            'description' => 'sometimes|string'
        ];
    }
}
