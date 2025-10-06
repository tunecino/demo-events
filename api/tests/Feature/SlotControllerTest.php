<?php

use App\Models\Event;
use App\Models\Slot;
use App\Enums\SlotStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it("puts on hold an available slot", function () {
    $event = Event::factory()->create();
    $slot = Slot::factory()->create([
        "event_id" => $event->id,
        "status" => SlotStatus::Available->value,
    ]);

    $response = $this->putJson(
        "/api/events/{$event->id}/slots/{$slot->id}/hold",
    );

    $response->assertOk();
    $slot->refresh();

    expect($slot->user_id)->not->toBeNull();
    expect($slot->user_id)->toBeString();
    expect($slot->status->value)->toBe(SlotStatus::Hold->value);
});

it("fails to put on hold a slot belonging to another event", function () {
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

it("fails to put on hold an already held slot", function () {
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

it("fails to put on hold a booked slot", function () {
    $event = Event::factory()->create();
    $slot = Slot::factory()->create([
        "event_id" => $event->id,
        "status" => SlotStatus::Booked->value,
    ]);

    $response = $this->putJson(
        "/api/events/{$event->id}/slots/{$slot->id}/hold",
    );

    $response
        ->assertStatus(422)
        ->assertJson(["error" => "Only available slots can be held"]);
});

it("can book a held slot by you", function () {
    $event = Event::factory()->create();
    $user_id = browser_user_id();
    $slot = Slot::factory()->create([
        "event_id" => $event->id,
        "status" => SlotStatus::Hold->value,
        "user_id" => $user_id,
    ]);

    $response = $this->putJson(
        "/api/events/{$event->id}/slots/{$slot->id}/book",
    );

    $response->assertOk();
    $slot->refresh();

    expect($slot->user_id)->not->toBeNull();
    expect($slot->user_id)->toBeString();
    expect($slot->user_id)->toBe($user_id);
    expect($slot->status->value)->toBe(SlotStatus::Booked->value);
});

it("fails to book an available slot", function () {
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

it("fails to book a slot twice", function () {
    $event = Event::factory()->create();
    $slot = Slot::factory()->create([
        "event_id" => $event->id,
        "status" => SlotStatus::Booked->value,
    ]);

    $response = $this->putJson(
        "/api/events/{$event->id}/slots/{$slot->id}/book",
    );

    $response
        ->assertStatus(422)
        ->assertJson(["error" => "Only held slots can be booked"]);
});

it("fails to book a held slot by someone else", function () {
    $event = Event::factory()->create();
    $slot = Slot::factory()->create([
        "event_id" => $event->id,
        "status" => SlotStatus::Hold->value,
        "user_id" => "barracuda",
    ]);

    // simulate another browser/user
    $response = $this->putJson(
        "/api/events/{$event->id}/slots/{$slot->id}/book",
        [],
        ["User-Agent" => "Different Browser"],
    );

    $response->assertStatus(403)->assertJson(["message" => "Forbidden action"]);

    $slot = $slot->fresh();
    expect($slot->status->value)->toBe(SlotStatus::Hold->value);
    expect($slot->user_id)->toBe("barracuda");
});

it("can set back to available a held slot by you", function () {
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

    $slot = $slot->fresh();
    expect($slot->status->value)->toBe(SlotStatus::Available->value);
    expect($slot->user_id)->toBeNull();
});

it("fails to release a slot that is not on hold", function () {
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

it("fails to release a slot held by another user", function () {
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
