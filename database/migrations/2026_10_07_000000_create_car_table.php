<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('car', function (Blueprint $table): void {
            $table->string('ident', 64)->primary();
            $table->decimal('battery_voltage', 8, 3);
            $table->unsignedBigInteger('channel_id')->index();
            $table->unsignedBigInteger('device_id')->index();
            $table->string('device_name');
            $table->unsignedBigInteger('device_type_id');
            $table->boolean('engine_ignition_status');
            $table->unsignedSmallInteger('event_priority_enum');
            $table->decimal('external_powersource_voltage', 8, 3);
            $table->unsignedSmallInteger('gnss_state_enum');
            $table->boolean('gnss_status');
            $table->unsignedBigInteger('gsm_cellid');
            $table->unsignedInteger('gsm_lac');
            $table->unsignedSmallInteger('gsm_mcc');
            $table->unsignedSmallInteger('gsm_mnc');
            $table->string('gsm_operator_code', 16);
            $table->unsignedTinyInteger('gsm_signal_level');
            $table->boolean('movement_status');
            $table->string('peer');
            $table->decimal('position_altitude', 10, 3);
            $table->decimal('position_direction', 6, 2);
            $table->decimal('position_hdop', 8, 3);
            $table->decimal('position_latitude', 10, 7);
            $table->decimal('position_longitude', 11, 7);
            $table->unsignedSmallInteger('position_satellites');
            $table->decimal('position_speed', 10, 3);
            $table->boolean('position_valid');
            $table->unsignedBigInteger('protocol_id');
            $table->decimal('server_timestamp', 16, 6);
            $table->unsignedBigInteger('timestamp');
            $table->decimal('vehicle_mileage', 14, 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car');
    }
};
