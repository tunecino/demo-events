<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind Repositories
        $this->app->bind(
            \Src\Event\Domain\Repositories\SlotRepositoryInterface::class,
            \Src\Event\Infrastructure\Persistence\EloquentSlotRepository::class,
        );
        $this->app->bind(
            \Src\Event\Domain\Repositories\EventRepositoryInterface::class,
            \Src\Event\Infrastructure\Persistence\EloquentEventRepository::class,
        );

        // Bind Services
        $this->app->bind(
            \Src\Event\Application\Services\UserIdProviderInterface::class,
            \Src\Event\Infrastructure\Services\BrowserUserIdProvider::class,
        );
    }
}
