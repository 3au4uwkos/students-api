<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateGroupCurriculumRequest
 *
 *
 *
 * @author      Matvei Zaitsev <3au4uwkos@gmail.com>
 * @category
 * @package     App\Http\Requests
 */
class UpdateGroupCurriculumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lectures' => 'required|array|min:1',
            'lectures.*' => 'required|integer|exists:lectures,id'
        ];
    }

    public function messages(): array
    {
        return [
            'lectures.required' => 'Список лекций обязателен',
            'lectures.array' => 'Лекции должны быть в виде массива',
            'lectures.min' => 'Должна быть хотя бы одна лекция',
            'lectures.*.exists' => 'Лекция с ID :input не существует'
        ];
    }
}
