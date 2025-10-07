<?php
namespace Src\Event\Application\UseCases;

use Src\Event\Domain\Exceptions\SlotNotFoundException;
use Src\Event\Domain\Repositories\SlotRepositoryInterface;
use Src\Event\Application\DTO\SlotData;

final class UnholdSlotUseCase
{
    public function __construct(private readonly SlotRepositoryInterface $repository) {}

    public function execute(string $eventId, string $slotId, string $userId): SlotData
    {
        $slot = $this->repository->findByEventAndSlotId($eventId, $slotId);
        if (!$slot) {
            throw new SlotNotFoundException('Slot does not belong to this event.');
        }
        $slot->unhold($userId);
        $this->repository->save($slot);
        return SlotData::fromEntity($slot);
    }
}
