<?php

namespace Tests\Feature;

use App\Events\CarPositionChanged;
use App\Listeners\BroadcastCarPositionUpdated;
use App\Models\Car;
use Illuminate\Broadcasting\AnonymousEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TruckerEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_receives_cars_from_the_database(): void
    {
        Event::fake([CarPositionChanged::class]);

        $payload = $this->validPayload();
        $this->postJson('/api/truckers', $payload)->assertCreated();

        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('index')
                ->has('cars', 1)
                ->where('cars.0.ident', $payload['ident'])
                ->where('cars.0.position_latitude', 43.955218)
                ->where('cars.0.position_longitude', 37.661918));
    }

    public function test_it_stores_vehicle_telemetry_with_dotted_keys(): void
    {
        Event::fake([CarPositionChanged::class]);

        $response = $this->postJson('/api/truckers', $this->validPayload());

        $response
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.device_id', 182083)
            ->assertJsonPath('data.position_latitude', 43.955218)
            ->assertJsonPath('data.engine_ignition_status', false)
            ->assertJsonMissingPath('message');

        $this->assertDatabaseHas('car', [
            'device_id' => 182083,
            'ident' => '111115555599999',
            'gsm_operator_code' => '25701',
        ]);

        Event::assertDispatched(
            CarPositionChanged::class,
            fn (CarPositionChanged $event): bool => $event->action === 'created'
                && $event->car->ident === '111115555599999',
        );
    }

    public function test_it_rejects_invalid_vehicle_telemetry(): void
    {
        $payload = $this->validPayload();
        $payload['position.latitude'] = 100;
        unset($payload['device.id']);

        $this->postJson('/api/truckers', $payload)
            ->assertUnprocessable()
            ->assertExactJson([
                'success' => false,
                'error' => 'The device id field is required. (and 1 more error)',
            ]);

        $this->assertDatabaseCount('car', 0);
    }

    public function test_it_shows_vehicle_telemetry_by_ident(): void
    {
        $payload = $this->validPayload();
        $this->postJson('/api/truckers', $payload)->assertCreated();

        Event::fake([CarPositionChanged::class]);

        $this->getJson('/api/truckers/'.$payload['ident'])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.ident', $payload['ident'])
            ->assertJsonPath('data.device_name', 'Vehicle');

        Event::assertDispatched(
            CarPositionChanged::class,
            fn (CarPositionChanged $event): bool => $event->action === 'retrieved'
                && $event->car->ident === $payload['ident'],
        );
    }

    public function test_it_updates_vehicle_telemetry_by_ident(): void
    {
        $payload = $this->validPayload();
        $this->postJson('/api/truckers', $payload)->assertCreated();

        Event::fake([CarPositionChanged::class]);

        $this->patchJson('/api/truckers/'.$payload['ident'], [
            'device.name' => 'Updated Vehicle',
            'movement.status' => true,
            'position.speed' => 48.5,
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.ident', $payload['ident'])
            ->assertJsonPath('data.device_name', 'Updated Vehicle')
            ->assertJsonPath('data.movement_status', true)
            ->assertJsonPath('data.position_speed', 48.5);

        $this->assertDatabaseHas('car', [
            'ident' => $payload['ident'],
            'device_name' => 'Updated Vehicle',
            'movement_status' => true,
            'position_speed' => 48.5,
        ]);

        Event::assertDispatched(
            CarPositionChanged::class,
            fn (CarPositionChanged $event): bool => $event->action === 'updated'
                && $event->car->ident === $payload['ident']
                && $event->car->position_latitude === 43.955218,
        );
    }

    public function test_it_returns_paginated_car_update_history(): void
    {
        Event::fake([CarPositionChanged::class]);

        $payload = $this->validPayload();
        $this->postJson('/api/truckers', $payload)->assertCreated();

        $this->patchJson('/api/truckers/'.$payload['ident'], [
            'position.speed' => 25.5,
        ])->assertOk();

        $this->patchJson('/api/truckers/'.$payload['ident'], [
            'position.speed' => 50.5,
        ])->assertOk();

        $this->getJson('/api/truckers/'.$payload['ident'].'/history?per_page=1')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.current_page', 1)
            ->assertJsonPath('data.per_page', 1)
            ->assertJsonPath('data.total', 2)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.car_ident', $payload['ident'])
            ->assertJsonPath('data.data.0.changes.position_speed', 50.5)
            ->assertJsonPath('data.data.0.snapshot.position_speed', 50.5);

        $this->assertDatabaseCount('car_logs', 2);
    }

    public function test_listener_broadcasts_the_car_position_to_the_public_channel(): void
    {
        Event::fake([AnonymousEvent::class]);

        $car = new Car;
        $car->forceFill([
            'ident' => '111115555599999',
            'position_latitude' => 43.955218,
            'position_longitude' => 37.661918,
            'position_speed' => 48.5,
        ]);

        (new BroadcastCarPositionUpdated)->handle(
            new CarPositionChanged($car, 'updated'),
        );

        Event::assertDispatched(
            AnonymousEvent::class,
            function (AnonymousEvent $event): bool {
                $channels = $event->broadcastOn();
                $payload = $event->broadcastWith();

                return $event->broadcastAs() === config('car-broadcasting.event')
                    && (string) $channels[0] === config('car-broadcasting.channel')
                    && $payload['action'] === 'updated'
                    && $payload['car']['ident'] === '111115555599999'
                    && $payload['car']['position_latitude'] === 43.955218
                    && $payload['car']['position_longitude'] === 37.661918;
            },
        );
    }

    /**
     * @return array<string, bool|float|int|string>
     */
    private function validPayload(): array
    {
        return [
            'battery.voltage' => 3.938,
            'channel.id' => 429,
            'device.id' => 182083,
            'device.name' => 'Vehicle',
            'device.type.id' => 744,
            'engine.ignition.status' => false,
            'event.priority.enum' => 0,
            'external.powersource.voltage' => 12.64,
            'gnss.state.enum' => 1,
            'gnss.status' => true,
            'gsm.cellid' => 11511,
            'gsm.lac' => 108,
            'gsm.mcc' => 257,
            'gsm.mnc' => 1,
            'gsm.operator.code' => '25701',
            'gsm.signal.level' => 80,
            'ident' => '111115555599999',
            'movement.status' => false,
            'peer' => '185.213.2.10:59924',
            'position.altitude' => 244,
            'position.direction' => 281,
            'position.hdop' => 0.3,
            'position.latitude' => 43.955218,
            'position.longitude' => 37.661918,
            'position.satellites' => 15,
            'position.speed' => 0,
            'position.valid' => true,
            'protocol.id' => 14,
            'server.timestamp' => 1678346072.792816,
            'timestamp' => 1678346071,
            'vehicle.mileage' => 36713.703,
        ];
    }
}
