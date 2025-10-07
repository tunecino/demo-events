<?php

namespace Tests\Unit;

use Src\Event\Domain\Entities\Slot;
use Src\Event\Domain\Enums\SlotStatus;
use Src\Event\Domain\Exceptions\SlotNotAvailableException;
use Src\Event\Domain\Exceptions\UserMismatchException;

it("can hold an available slot", function () {
    $slot = new Slot(
        id: "slot-1",
        eventId: "event-1",
        startAt: new \DateTimeImmutable(),
        endAt: new \DateTimeImmutable(),
        status: SlotStatus::Available,
        userId: null,
    );

    $slot->hold("user-123");

    expect($slot->status)->toBe(SlotStatus::Hold);
    expect($slot->userId)->toBe("user-123");
});

it("throws an exception when holding a booked slot", function () {
    $slot = new Slot(
        id: "slot-1",
        eventId: "event-1",
        startAt: new \DateTimeImmutable(),
        endAt: new \DateTimeImmutable(),
        status: SlotStatus::Booked,
        userId: "user-abc",
    );

    $slot->hold("user-123");
})->throws(
    SlotNotAvailableException::class,
    "Only available slots can be held",
);

it(
    "throws an exception when unholding a slot held by another user",
    function () {
        $slot = new Slot(
            id: "slot-1",
            eventId: "event-1",
            startAt: new \DateTimeImmutable(),
            endAt: new \DateTimeImmutable(),
            status: SlotStatus::Hold,
            userId: "user-abc",
        );

        $slot->unhold("user-123");
    },
)->throws(
    UserMismatchException::class,
    "You can only release slots you have held.",
);
