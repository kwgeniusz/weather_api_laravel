<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class WeatherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'city' => ['required', 'string', 'max:255'],
            'language' => ['sometimes', 'string', 'size:2'],
            'units' => ['sometimes', 'string', 'in:metric,imperial'],
            'days' => ['sometimes', 'integer', 'between:1,7'],
        ];
    }
}
