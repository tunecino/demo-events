<?php

namespace Src\Event\Infrastructure\Persistence;

use App\Models\Slot as EloquentSlotModel;
use Src\Event\Domain\Entities\Slot as DomainSlot;
use Src\Event\Domain\Repositories\SlotRepositoryInterface;

final class EloquentSlotRepository implements SlotRepositoryInterface
{
    public function findByEventAndSlotId(string $eventId, string $slotId): ?DomainSlot
    {
        $eloquentSlot = EloquentSlotModel::where('event_id', $eventId)->find($slotId);

        if (!$eloquentSlot) {
            return null;
        }

        return new DomainSlot(
            id: $eloquentSlot->id,
            eventId: $eloquentSlot->event_id,
            startAt: new \DateTimeImmutable($eloquentSlot->start_at),
            endAt: new \DateTimeImmutable($eloquentSlot->end_at),
            status: $eloquentSlot->status,
            userId: $eloquentSlot->user_id
        );
    }

    public function save(DomainSlot $slot): void
    {
        $eloquentSlot = EloquentSlotModel::find($slot->id);
        if ($eloquentSlot) {
            $eloquentSlot->status = $slot->status;
            $eloquentSlot->user_id = $slot->userId;
            $eloquentSlot->save();
        }
    }
}