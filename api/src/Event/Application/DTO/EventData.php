<?php

namespace Src\Event\Application\DTO;

use Src\Event\Domain\Entities\Event;

class EventData
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $description,
        public readonly ?string $image,
        public readonly \DateTimeImmutable $startAt,
        public readonly \DateTimeImmutable $endAt,
        public readonly int $amount,
        public readonly string $currency,
        public array $slots,
    ) {}

    public static function fromEntity(Event $event): self
    {
        $slotData = [];
        foreach ($event->slots as $slot) {
            $slotData[] = SlotData::fromEntity($slot);
        }

        return new self(
            id: $event->id,
            name: $event->name,
            description: $event->description,
            image: $event->image,
            startAt: $event->startAt,
            endAt: $event->endAt,
            amount: $event->amount,
            currency: $event->currency,
            slots: $slotData
        );
    }
}