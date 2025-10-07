<?php

namespace Src\Event\Domain\Repositories;

use Src\Event\Domain\Entities\Slot;

interface SlotRepositoryInterface
{
    public function findByEventAndSlotId(string $eventId, string $slotId): ?Slot;
    
    public function save(Slot $slot): void;
}
