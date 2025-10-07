<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Src\Event\Application\DTO\EventData;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var EventData $this */
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "image" => $this->image,
            "start_at" => $this->startAt->format("Y-m-d\TH:i:s.u\Z"),
            "end_at" => $this->endAt->format("Y-m-d\TH:i:s.u\Z"),
            "amount" => $this->amount,
            "currency" => $this->currency,
            "slots" => SlotResource::collection($this->slots),
        ];
    }
}
