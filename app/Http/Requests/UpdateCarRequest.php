<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $carId = (int) ($this->route('id') ?? $this->route('car'));

        return [
            'brand' => ['sometimes', 'required', 'string', 'max:100'],
            'model' => ['sometimes', 'required', 'string', 'max:100'],
            'license_plate' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                Rule::unique('cars', 'license_plate')->ignore($carId),
            ],
        ];
    }

}
