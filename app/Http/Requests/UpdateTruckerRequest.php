<?php

namespace App\Http\Requests;

class UpdateTruckerRequest extends CarTelemetryRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            ...$this->telemetryRules('sometimes'),
            'ident' => ['prohibited'],
            'route_ident' => ['required', 'string', 'max:64'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'ident.prohibited' => 'The vehicle identifier cannot be changed.',
            'route_ident.required' => 'The vehicle identifier is required.',
            'position_latitude.between' => 'The latitude must be between -90 and 90.',
            'position_longitude.between' => 'The longitude must be between -180 and 180.',
        ];
    }

    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();

        $this->merge([
            'route_ident' => $this->route('ident'),
        ]);
    }
}
