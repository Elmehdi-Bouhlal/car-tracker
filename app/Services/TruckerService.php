<?php

namespace App\Services;

use App\Events\CarPositionChanged;
use App\Models\Car;
use App\Models\CarLog;
use App\Repositories\CarLogRepository;
use App\Repositories\CarRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TruckerService
{
    public function __construct(
        private readonly CarRepository $carRepository,
        private readonly CarLogRepository $carLogRepository,
    ) {}

    /**
     * Get all vehicles ordered by their latest update.
     *
     * @return Collection<int, Car>
     */
    public function getAll(): Collection
    {
        return $this->carRepository->getAll();
    }

    /**
     * Store validated vehicle telemetry.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Car
    {
        return $this->dispatchPositionChanged(
            $this->carRepository->create($data),
            (string) config('car-broadcasting.actions.created'),
        );
    }

    /**
     * Get a vehicle by its identifier.
     */
    public function findByIdent(string $ident): Car
    {
        return $this->dispatchPositionChanged(
            $this->carRepository->findByIdent($ident),
            (string) config('car-broadcasting.actions.retrieved'),
        );
    }

    /**
     * Update validated vehicle telemetry.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateByIdent(string $ident, array $data): Car
    {
        return DB::transaction(function () use ($ident, $data): Car {
            $car = $this->carRepository->updateByIdent($ident, $data);

            $this->carLogRepository->create($car, $data);

            return $this->dispatchPositionChanged(
                $car,
                (string) config('car-broadcasting.actions.updated'),
            );
        });
    }

    /**
     * Get the paginated update history for a car.
     *
     * @return LengthAwarePaginator<int, CarLog>
     */
    public function getHistory(string $ident, int $perPage): LengthAwarePaginator
    {
        $this->carRepository->findByIdent($ident);

        return $this->carLogRepository->paginateByIdent($ident, $perPage);
    }

    /**
     * Dispatch the car position event and return the car unchanged.
     */
    private function dispatchPositionChanged(
        Car $car,
        string $action,
    ): Car {
        CarPositionChanged::dispatch($car, $action);

        return $car;
    }
}
