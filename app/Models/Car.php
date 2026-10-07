<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property float $battery_voltage
 * @property int $channel_id
 * @property int $device_id
 * @property string $device_name
 * @property int $device_type_id
 * @property bool $engine_ignition_status
 * @property int $event_priority_enum
 * @property float $external_powersource_voltage
 * @property int $gnss_state_enum
 * @property bool $gnss_status
 * @property int $gsm_cellid
 * @property int $gsm_lac
 * @property int $gsm_mcc
 * @property int $gsm_mnc
 * @property string $gsm_operator_code
 * @property int $gsm_signal_level
 * @property string $ident
 * @property bool $movement_status
 * @property string $peer
 * @property float $position_altitude
 * @property float $position_direction
 * @property float $position_hdop
 * @property float $position_latitude
 * @property float $position_longitude
 * @property int $position_satellites
 * @property float $position_speed
 * @property bool $position_valid
 * @property int $protocol_id
 * @property float $server_timestamp
 * @property int $timestamp
 * @property float $vehicle_mileage
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */


#[Fillable([
    'battery_voltage',
    'channel_id',
    'device_id',
    'device_name',
    'device_type_id',
    'engine_ignition_status',
    'event_priority_enum',
    'external_powersource_voltage',
    'gnss_state_enum',
    'gnss_status',
    'gsm_cellid',
    'gsm_lac',
    'gsm_mcc',
    'gsm_mnc',
    'gsm_operator_code',
    'gsm_signal_level',
    'ident',
    'movement_status',
    'peer',
    'position_altitude',
    'position_direction',
    'position_hdop',
    'position_latitude',
    'position_longitude',
    'position_satellites',
    'position_speed',
    'position_valid',
    'protocol_id',
    'server_timestamp',
    'timestamp',
    'vehicle_mileage',
])]

class Car extends Model
{
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'ident';

    /**
     * The data type of the primary key.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'car';

    /** @return HasMany<CarLog, $this> */
    public function logs(): HasMany
    {
        return $this->hasMany(CarLog::class, 'car_ident', 'ident');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'battery_voltage' => 'float',
            'channel_id' => 'integer',
            'device_id' => 'integer',
            'device_type_id' => 'integer',
            'engine_ignition_status' => 'boolean',
            'event_priority_enum' => 'integer',
            'external_powersource_voltage' => 'float',
            'gnss_state_enum' => 'integer',
            'gnss_status' => 'boolean',
            'gsm_cellid' => 'integer',
            'gsm_lac' => 'integer',
            'gsm_mcc' => 'integer',
            'gsm_mnc' => 'integer',
            'gsm_signal_level' => 'integer',
            'movement_status' => 'boolean',
            'position_altitude' => 'float',
            'position_direction' => 'float',
            'position_hdop' => 'float',
            'position_latitude' => 'float',
            'position_longitude' => 'float',
            'position_satellites' => 'integer',
            'position_speed' => 'float',
            'position_valid' => 'boolean',
            'protocol_id' => 'integer',
            'server_timestamp' => 'float',
            'timestamp' => 'integer',
            'vehicle_mileage' => 'float',
        ];
    }
}
