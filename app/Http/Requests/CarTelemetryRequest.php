<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class CarTelemetryRequest extends FormRequest
{
    /** @var array<string, string> */
    private const FIELD_MAP = [
        'battery.voltage' => 'battery_voltage',
        'channel.id' => 'channel_id',
        'device.id' => 'device_id',
        'device.name' => 'device_name',
        'device.type.id' => 'device_type_id',
        'engine.ignition.status' => 'engine_ignition_status',
        'event.priority.enum' => 'event_priority_enum',
        'external.powersource.voltage' => 'external_powersource_voltage',
        'gnss.state.enum' => 'gnss_state_enum',
        'gnss.status' => 'gnss_status',
        'gsm.cellid' => 'gsm_cellid',
        'gsm.lac' => 'gsm_lac',
        'gsm.mcc' => 'gsm_mcc',
        'gsm.mnc' => 'gsm_mnc',
        'gsm.operator.code' => 'gsm_operator_code',
        'gsm.signal.level' => 'gsm_signal_level',
        'ident' => 'ident',
        'movement.status' => 'movement_status',
        'peer' => 'peer',
        'position.altitude' => 'position_altitude',
        'position.direction' => 'position_direction',
        'position.hdop' => 'position_hdop',
        'position.latitude' => 'position_latitude',
        'position.longitude' => 'position_longitude',
        'position.satellites' => 'position_satellites',
        'position.speed' => 'position_speed',
        'position.valid' => 'position_valid',
        'protocol.id' => 'protocol_id',
        'server.timestamp' => 'server_timestamp',
        'timestamp' => 'timestamp',
        'vehicle.mileage' => 'vehicle_mileage',
    ];

    /**
     * Normalize external telemetry keys for validation and persistence.
     */
    protected function prepareForValidation(): void
    {
        /** @var array<string, mixed> $input */
        $input = $this->all();
        $normalized = [];

        foreach (self::FIELD_MAP as $external => $internal) {
            if (array_key_exists($external, $input)) {
                $normalized[$internal] = $input[$external];

                continue;
            }

            $value = data_get($input, $external);

            if ($value !== null) {
                $normalized[$internal] = $value;
            }
        }

        $this->merge($normalized);
    }

    /**
     * Get the common telemetry validation rules.
     *
     * @return array<string, list<string>>
     */
    protected function telemetryRules(string $presence): array
    {
        return [
            'battery_voltage' => [$presence, 'numeric', 'between:0,99999.999'],
            'channel_id' => [$presence, 'integer', 'min:0'],
            'device_id' => [$presence, 'integer', 'min:0'],
            'device_name' => [$presence, 'string', 'max:255'],
            'device_type_id' => [$presence, 'integer', 'min:0'],
            'engine_ignition_status' => [$presence, 'boolean'],
            'event_priority_enum' => [$presence, 'integer', 'between:0,65535'],
            'external_powersource_voltage' => [$presence, 'numeric', 'between:0,99999.999'],
            'gnss_state_enum' => [$presence, 'integer', 'between:0,65535'],
            'gnss_status' => [$presence, 'boolean'],
            'gsm_cellid' => [$presence, 'integer', 'min:0'],
            'gsm_lac' => [$presence, 'integer', 'between:0,4294967295'],
            'gsm_mcc' => [$presence, 'integer', 'between:0,999'],
            'gsm_mnc' => [$presence, 'integer', 'between:0,999'],
            'gsm_operator_code' => [$presence, 'string', 'max:16'],
            'gsm_signal_level' => [$presence, 'integer', 'between:0,100'],
            'movement_status' => [$presence, 'boolean'],
            'peer' => [$presence, 'string', 'max:255'],
            'position_altitude' => [$presence, 'numeric', 'between:-9999999.999,9999999.999'],
            'position_direction' => [$presence, 'numeric', 'between:0,360'],
            'position_hdop' => [$presence, 'numeric', 'between:0,99999.999'],
            'position_latitude' => [$presence, 'numeric', 'between:-90,90'],
            'position_longitude' => [$presence, 'numeric', 'between:-180,180'],
            'position_satellites' => [$presence, 'integer', 'between:0,65535'],
            'position_speed' => [$presence, 'numeric', 'between:0,9999999.999'],
            'position_valid' => [$presence, 'boolean'],
            'protocol_id' => [$presence, 'integer', 'min:0'],
            'server_timestamp' => [$presence, 'numeric', 'between:0,9999999999.999999'],
            'timestamp' => [$presence, 'integer', 'min:0'],
            'vehicle_mileage' => [$presence, 'numeric', 'between:0,99999999999.999'],
        ];
    }
}
