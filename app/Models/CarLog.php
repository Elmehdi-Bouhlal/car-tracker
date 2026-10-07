<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $car_ident
 * @property array<string, mixed> $changes
 * @property array<string, mixed> $snapshot
 * @property Carbon|null $created_at
 * @property-read Car $car
 */
#[Fillable(['car_ident', 'changes', 'snapshot'])]
class CarLog extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'car_logs';

    /** @return BelongsTo<Car, $this> */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_ident', 'ident');
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'changes' => 'array',
            'snapshot' => 'array',
            'created_at' => 'datetime',
        ];
    }
}
