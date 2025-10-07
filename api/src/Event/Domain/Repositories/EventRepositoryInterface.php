<?php

namespace Src\Event\Domain\Repositories;

interface EventRepositoryInterface
{
    public function findAllWithSlots(): array;
}