<?php

namespace Src\Event\Infrastructure\Persistence;

use App\Models\Event as EloquentEventModel;
use Src\Event\Domain\Entities\Event as DomainEvent;
use Src\Event\Domain\Entities\Slot as DomainSlot;
use Src\Event\Domain\Repositories\EventRepositoryInterface;

final class EloquentEventRepository implements EventRepositoryInterface
{
    public function findAllWithSlots(): array
    {
        $eloquentEvents = EloquentEventModel::with('slots')->get();
        $domainEvents = [];

        foreach ($eloquentEvents as $eloquentEvent) {
            $domainSlots = [];
            foreach ($eloquentEvent->slots as $eloquentSlot) {
                $domainSlots[] = new DomainSlot(
                    id: $eloquentSlot->id,
                    eventId: $eloquentSlot->event_id,
                    startAt: new \DateTimeImmutable($eloquentSlot->start_at),
                    endAt: new \DateTimeImmutable($eloquentSlot->end_at),
                    status: $eloquentSlot->status,
                    userId: $eloquentSlot->user_id,
                );
            }
            
            $domainEvents[] = new DomainEvent(
                id: $eloquentEvent->id,
                name: $eloquentEvent->name,
                description: $eloquentEvent->description,
                image: $eloquentEvent->image,
                startAt: new \DateTimeImmutable($eloquentEvent->start_at),
                endAt: new \DateTimeImmutable($eloquentEvent->end_at),
                amount: $eloquentEvent->amount,
                currency: $eloquentEvent->currency,
                slots: $domainSlots
            );
        }

        return $domainEvents;
    }
}