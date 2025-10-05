<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "image" => $this->image,
            "start_at" => $this->start_at,
            "end_at" => $this->end_at,
            "amount" => $this->amount,
            "currency" => $this->currency,
            "slots" => SlotResource::collection($this->whenLoaded("slots")),
        ];
    }
}
