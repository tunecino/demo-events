<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Slot;
use App\Enums\SlotStatus;
use App\Http\Resources\SlotResource;
use Illuminate\Http\JsonResponse;

class SlotController extends Controller
{
    public function hold(Event $event, Slot $slot): JsonResponse
    {
        // Ensure slot belongs to the event
        if ($slot->event_id !== $event->id) {
            return response()->json(
                ["error" => "Slot does not belong to this event"],
                404,
            );
        }

        // Only available → hold
        if ($slot->status->value !== SlotStatus::Available->value) {
            return response()->json(
                ["error" => "Only available slots can be held"],
                422,
            );
        }

        $slot->update(["status" => SlotStatus::Hold->value]);

        return response()->json(["status" => "hold"]);
    }

    public function unhold(string $eventId, string $slotId)
    {
        $slot = Slot::where("event_id", $eventId)->findOrFail($slotId);

        if ($slot->status->value !== SlotStatus::Hold->value) {
            return response()->json(
                ["message" => "Only held slots can be released."],
                422,
            );
        }

        $currentUserId = browser_user_id();

        if ($slot->user_id !== $currentUserId) {
            return response()->json(
                ["message" => "You can only release slots you have held."],
                403,
            );
        }

        $slot->update([
            "status" => SlotStatus::Available->value,
            "user_id" => null,
        ]);

        return response()->json([
            "message" => "Slot released successfully.",
            "data" => new SlotResource($slot),
        ]);
    }

    public function book(Event $event, Slot $slot): JsonResponse
    {
        if ($slot->event_id !== $event->id) {
            return response()->json(
                ["error" => "Slot does not belong to this event"],
                404,
            );
        }

        if ($slot->status->value !== SlotStatus::Hold->value) {
            return response()->json(
                ["error" => "Only held slots can be booked"],
                422,
            );
        }

        $slot->update([
            "status" => SlotStatus::Booked->value,
            "user_id" => browser_user_id(),
        ]);

        return response()->json([
            "status" => "booked",
            "user_id" => $slot->user_id,
        ]);
    }
}
