<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Src\Event\Application\DTO\SlotData;

class SlotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var SlotData $this */
        return [
            "id" => $this->id,
            "start_at" => $this->startAt->format("Y-m-d\TH:i:s.u\Z"),
            "end_at" => $this->endAt->format("Y-m-d\TH:i:s.u\Z"),
            "status" => $this->status, // Already a string from DTO
            "user_id" => $this->userId,
        ];
    }
}
