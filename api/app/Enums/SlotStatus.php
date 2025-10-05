<?php

namespace App\Enums;

enum SlotStatus: int
{
    case Available = 1;
    case Hold = 2;
    case Booked = 3;

    public function label(): string
    {
        return match ($this) {
            self::Available => "available",
            self::Hold => "hold",
            self::Booked => "booked",
        };
    }
}
