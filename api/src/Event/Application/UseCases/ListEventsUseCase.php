<?php
namespace Src\Event\Application\UseCases;

use Src\Event\Domain\Repositories\EventRepositoryInterface;
use Src\Event\Application\DTO\EventData;

final class ListEventsUseCase
{
    public function __construct(private readonly EventRepositoryInterface $repository) {}

    public function execute(): array
    {
        $events = $this->repository->findAllWithSlots();
        return array_map(fn($event) => EventData::fromEntity($event), $events);
    }
}
