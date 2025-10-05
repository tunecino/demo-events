<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SlotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "start_at" => $this->start_at,
            "end_at" => $this->end_at,
            "status" => $this->status->label(),
            "user_id" => $this->user_id,
        ];
    }
}
