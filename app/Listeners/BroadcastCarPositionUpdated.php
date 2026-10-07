<?php

namespace App\Listeners;

use App\Events\CarPositionChanged;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Support\Facades\Broadcast;

class BroadcastCarPositionUpdated implements ShouldQueueAfterCommit
{
    /**
     * The number of times the queued listener may be attempted.
     */
    public int $tries = 3;

    /**
     * Handle the event.
     */
    public function handle(CarPositionChanged $event): void
    {
        Broadcast::on((string) config('car-broadcasting.channel'))
            ->as((string) config('car-broadcasting.event'))
            ->with([
                'action' => $event->action,
                'car' => $event->car->toArray(),
            ])
            ->sendNow();
    }
}
