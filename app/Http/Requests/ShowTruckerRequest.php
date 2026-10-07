<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowTruckerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'ident' => ['required', 'string', 'max:64'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'ident.required' => 'The vehicle identifier is required.',
            'ident.max' => 'The vehicle identifier may not exceed 64 characters.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'ident' => $this->route('ident'),
        ]);
    }
}
