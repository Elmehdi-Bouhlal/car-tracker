<?php

namespace App\Repositories;

use App\Models\Car;
use Illuminate\Database\Eloquent\Collection;

class CarRepository
{
    /**
     * Get all cars ordered by their latest update.
     *
     * @return Collection<int, Car>
     */
    public function getAll(): Collection
    {
        return Car::query()
            ->latest('updated_at')
            ->get();
    }

    /**
     * Find a car by its identifier.
     */
    public function findByIdent(string $ident): Car
    {
        return Car::query()->findOrFail($ident);
    }

    /**
     * Persist a vehicle telemetry record.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Car
    {
        return Car::query()->create($data);
    }

    /**
     * Update a vehicle telemetry record.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateByIdent(string $ident, array $data): Car
    {
        $car = $this->findByIdent($ident);
        $car->update($data);

        return $car->refresh();
    }
}
