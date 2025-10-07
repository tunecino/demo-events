<?php

namespace Src\Event\Domain\Entities;

final class Event
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
        public array $slots = []
    ) {}
}