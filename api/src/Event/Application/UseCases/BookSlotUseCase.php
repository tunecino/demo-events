<?php
namespace Src\Event\Application\UseCases;

use Src\Event\Domain\Exceptions\SlotNotFoundException;
use Src\Event\Domain\Repositories\SlotRepositoryInterface;

final class BookSlotUseCase
{
    public function __construct(private readonly SlotRepositoryInterface $repository) {}

    public function execute(string $eventId, string $slotId, string $userId): void
    {
        $slot = $this->repository->findByEventAndSlotId($eventId, $slotId);
        if (!$slot) {
            throw new SlotNotFoundException('Slot does not belong to this event.');
        }
        $slot->book($userId);
        $this->repository->save($slot);
    }
}