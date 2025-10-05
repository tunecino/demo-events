<?php

use App\Models\Event;
use App\Models\Slot;
use App\Enums\SlotStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it("can hold an available slot", function () {
    $event = Event::factory()->create();
    $slot = Slot::factory()->create([
        "event_id" => $event->id,
        "status" => SlotStatus::Available->value,
    ]);

    $response = $this->putJson(
        "/api/events/{$event->id}/slots/{$slot->id}/hold",
    );

    $response->assertOk()->assertJson(["status" => "hold"]);

    expect($slot->fresh()->status->value)->toBe(SlotStatus::Hold->value);
});

it("cannot hold a non-available slot", function () {
    $event = Event::factory()->create();
    $slot = Slot::factory()->create([
        "event_id" => $event->id,
        "status" => SlotStatus::Hold->value,
    ]);

    $response = $this->putJson(
        "/api/events/{$event->id}/slots/{$slot->id}/hold",
    );

    $response
        ->assertStatus(422)
        ->assertJson(["error" => "Only available slots can be held"]);
});

it("can book a held slot", function () {
    $event = Event::factory()->create();
    $slot = Slot::factory()->create([
        "event_id" => $event->id,
        "status" => SlotStatus::Hold->value,
    ]);

    $response = $this->putJson(
        "/api/events/{$event->id}/slots/{$slot->id}/book",
    );

    $response->assertOk()->assertJson(["status" => "booked"]);

    expect($slot->fresh()->status->value)->toBe(SlotStatus::Booked->value);
});

it("cannot book an available slot", function () {
    $event = Event::factory()->create();
    $slot = Slot::factory()->create([
        "event_id" => $event->id,
        "status" => SlotStatus::Available->value,
    ]);

    $response = $this->putJson(
        "/api/events/{$event->id}/slots/{$slot->id}/book",
    );

    $response
        ->assertStatus(422)
        ->assertJson(["error" => "Only held slots can be booked"]);
});

it("fails if slot does not belong to event", function () {
    $eventA = Event::factory()->create();
    $eventB = Event::factory()->create();
    $slot = Slot::factory()->create([
        "event_id" => $eventB->id,
        "status" => SlotStatus::Available->value,
    ]);

    $response = $this->putJson(
        "/api/events/{$eventA->id}/slots/{$slot->id}/hold",
    );

    $response
        ->assertStatus(404)
        ->assertJson(["error" => "Slot does not belong to this event"]);
});

it("sets user_id when booking a slot", function () {
    $event = Event::factory()->create();
    $slot = Slot::factory()->create([
        "event_id" => $event->id,
        "status" => SlotStatus::Hold->value,
    ]);

    $userAgent = "Mozilla/5.0 (DemoTest)";
    $ip = "123.45.67.89";

    $response = $this->putJson(
        "/api/events/{$event->id}/slots/{$slot->id}/book",
        [],
        ["User-Agent" => $userAgent, "X-Forwarded-For" => $ip],
    );

    $response->assertOk();
    $slot->refresh();

    expect($slot->user_id)->not->toBeNull();
    expect($slot->user_id)->toBeString();
});

it("can release a held slot back to available", function () {
    $event = Event::factory()->create();

    $currentUserId = browser_user_id();

    $slot = Slot::factory()->create([
        "event_id" => $event->id,
        "status" => SlotStatus::Hold->value,
        "user_id" => $currentUserId,
    ]);

    $response = $this->deleteJson(
        "/api/events/{$event->id}/slots/{$slot->id}/hold",
    );

    $response->assertOk()->assertJson([
        "message" => "Slot released successfully.",
        "data" => ["id" => $slot->id],
    ]);

    expect($slot->fresh()->status->value)->toBe(SlotStatus::Available->value);
    expect($slot->fresh()->user_id)->toBeNull();
});

it("cannot release a slot that is not on hold", function () {
    $event = Event::factory()->create();
    $availableSlot = Slot::factory()->create([
        "event_id" => $event->id,
        "status" => SlotStatus::Available->value,
    ]);
    $bookedSlot = Slot::factory()->create([
        "event_id" => $event->id,
        "status" => SlotStatus::Booked->value,
    ]);

    $this->deleteJson(
        "/api/events/{$event->id}/slots/{$availableSlot->id}/hold",
    )
        ->assertStatus(422)
        ->assertJson(["message" => "Only held slots can be released."]);

    $this->deleteJson("/api/events/{$event->id}/slots/{$bookedSlot->id}/hold")
        ->assertStatus(422)
        ->assertJson(["message" => "Only held slots can be released."]);
});

it("cannot release a slot held by another user", function () {
    $event = Event::factory()->create();
    $slot = Slot::factory()->create([
        "event_id" => $event->id,
        "status" => SlotStatus::Hold->value,
        "user_id" => "barracuda",
    ]);

    // simulate another browser/user
    $response = $this->deleteJson(
        "/api/events/{$event->id}/slots/{$slot->id}/hold",
        [],
        ["User-Agent" => "Different Browser"],
    );

    $response->assertStatus(403)->assertJson([
        "message" => "You can only release slots you have held.",
    ]);

    expect($slot->fresh()->status->value)->toBe(SlotStatus::Hold->value);
});
