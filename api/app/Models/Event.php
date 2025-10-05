<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = "string";

    public function slots()
    {
        return $this->hasMany(Slot::class);
    }
}
