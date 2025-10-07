<?php

namespace Src\Event\Domain\Entities;

use Src\Event\Domain\Enums\SlotStatus;
use Src\Event\Domain\Exceptions\SlotNotAvailableException;
use Src\Event\Domain\Exceptions\SlotNotHeldException;
use Src\Event\Domain\Exceptions\UserMismatchException;

final class Slot
{
    public function __construct(
        public readonly string $id,
        public readonly string $eventId,
        public \DateTimeImmutable $startAt,
        public \DateTimeImmutable $endAt,
        public SlotStatus $status,
        public ?string $userId
    ) {}

    public function hold(string $userId): void
    {
        if ($this->status !== SlotStatus::Available) {
            throw new SlotNotAvailableException('Only available slots can be held');
        }
        $this->status = SlotStatus::Hold;
        $this->userId = $userId;
    }

    public function unhold(string $userId): void
    {
        if ($this->status !== SlotStatus::Hold) {
            throw new SlotNotHeldException('Only held slots can be released.');
        }
        if ($this->userId !== $userId) {
            throw new UserMismatchException('You can only release slots you have held.');
        }
        $this->status = SlotStatus::Available;
        $this->userId = null;
    }

    public function book(string $userId): void
    {
        if ($this->status !== SlotStatus::Hold) {
            throw new SlotNotHeldException('Only held slots can be booked.');
        }
        if ($this->userId !== $userId) {
            throw new UserMismatchException('You can only book slots you have held.');
        }
        $this->status = SlotStatus::Booked;
    }
}