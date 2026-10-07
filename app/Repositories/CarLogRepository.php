<?php

namespace App\Repositories;

use App\Models\Car;
use App\Models\CarLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CarLogRepository
{
    /**
     * Record a car update.
     *
     * @param  array<string, mixed>  $changes
     */
    public function create(Car $car, array $changes): CarLog
    {
        return CarLog::query()->create([
            'car_ident' => $car->ident,
            'changes' => $changes,
            'snapshot' => $car->attributesToArray(),
        ]);
    }

    /**
     * Get a car's update history.
     *
     * @return LengthAwarePaginator<int, CarLog>
     */
    public function paginateByIdent(string $ident, int $perPage): LengthAwarePaginator
    {
        return CarLog::query()
            ->where('car_ident', $ident)
            ->latest('created_at')
            ->latest('id')
            ->paginate($perPage);
    }
}
