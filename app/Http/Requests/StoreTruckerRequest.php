<?php

namespace App\Http\Requests;

class StoreTruckerRequest extends CarTelemetryRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            ...$this->telemetryRules('required'),
            'ident' => ['required', 'string', 'max:64', 'unique:car,ident'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'ident.required' => 'The vehicle identifier is required.',
            'ident.unique' => 'A car with this identifier already exists.',
            'position_latitude.between' => 'The latitude must be between -90 and 90.',
            'position_longitude.between' => 'The longitude must be between -180 and 180.',
        ];
    }
}
