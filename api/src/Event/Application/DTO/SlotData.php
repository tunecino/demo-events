<?php

namespace Src\Event\Application\DTO;

use Src\Event\Domain\Entities\Slot;

class SlotData
{
    public function __construct(
        public readonly string $id,
        public readonly \DateTimeImmutable $startAt,
        public readonly \DateTimeImmutable $endAt,
        public readonly string $status,
        public readonly ?string $userId,
    ) {}

    public static function fromEntity(Slot $slot): self
    {
        return new self(
            id: $slot->id,
            startAt: $slot->startAt,
            endAt: $slot->endAt,
            status: $slot->status->label(),
            userId: $slot->userId,
        );
    }
}