<?php

namespace Src\Event\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Src\Event\Application\UseCases\ListEventsUseCase;

final class EventController extends Controller
{
    public function __construct(private readonly ListEventsUseCase $listEventsUseCase) {}

    public function index(): AnonymousResourceCollection
    {
        $events = $this->listEventsUseCase->execute();
        return EventResource::collection($events);
    }
}