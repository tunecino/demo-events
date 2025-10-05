<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\SlotStatus;

class Slot extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = "string";

    protected $casts = [
        "status" => SlotStatus::class,
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
