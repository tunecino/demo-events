<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $events = [
            [
                "name" => "Ping Pong Tournament",
                "description" =>
                    "Friendly ping pong games in Santa Cruz de Tenerife.",
                "currency" => "EUR",
                "amount" => 1000,
                "image" =>
                    "https://images.unsplash.com/photo-1576617497557-22895ee5930b?q=80&w=600", // ping pong
            ],
            [
                "name" => "Hiking in Anaga",
                "description" => "Guided hiking trip through Anaga hills.",
                "currency" => "EUR",
                "amount" => 2000,
                "image" =>
                    "https://plus.unsplash.com/premium_photo-1674173759138-f13ac19b07b6?w=600", // hiking
            ],
            [
                "name" => "Fishing Day",
                "description" => "Boat fishing near Tenerife coast.",
                "currency" => "EUR",
                "amount" => 3000,
                "image" =>
                    "https://images.unsplash.com/photo-1556919531-ddf2d5a3ee34?q=80&w=600", // fishing
            ],
            [
                "name" => "Bike Ride",
                "description" => "City bike tour around Tenerife.",
                "currency" => "EUR",
                "amount" => 1500,
                "image" =>
                    "https://images.unsplash.com/photo-1560928762-1c0eea9ab886?w=600", // biking
            ],
        ];

        // 4 events, 5 slots per event

        foreach ($events as $index => $data) {
            $eventId = (string) Str::uuid();
            $eventStart = $now->copy()->addDays($index)->setTime(9, 0);
            $eventEnd = $eventStart->copy()->addHours(5);

            DB::table("events")->insert([
                "id" => $eventId,
                "name" => $data["name"],
                "description" => $data["description"],
                "image" => $data["image"],
                "start_at" => $eventStart,
                "end_at" => $eventEnd,
                "amount" => $data["amount"],
                "currency" => $data["currency"],
                "created_at" => $now,
                "updated_at" => $now,
            ]);

            $slots = [];
            for ($i = 0; $i < 5; $i++) {
                $slotStart = $eventStart->copy()->addHours($i);
                $slotEnd = $slotStart->copy()->addHour();

                $slots[] = [
                    "id" => (string) Str::uuid(),
                    "event_id" => $eventId,
                    "user_id" => null,
                    "start_at" => $slotStart,
                    "end_at" => $slotEnd,
                    "status" => 1, // available. w'll also need 'hold' & 'booked'
                    "created_at" => $now,
                    "updated_at" => $now,
                ];
            }

            DB::table("slots")->insert($slots);
        }
    }
}
