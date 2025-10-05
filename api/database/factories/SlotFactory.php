<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Event;
use App\Models\Slot;
use App\Enums\SlotStatus;

class SlotFactory extends Factory
{
    protected $model = Slot::class;

    public function definition(): array
    {
        return [
            "id" => (string) Str::uuid(),
            "event_id" => Event::factory(),
            "start_at" => now(),
            "end_at" => now()->addHour(),
            "status" => SlotStatus::Available->value,
        ];
    }
}
