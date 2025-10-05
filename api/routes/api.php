<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\SlotController;

Route::get('events', [EventController::class, 'index']);

Route::prefix('events/{event}')->group(function () {
    Route::put('slots/{slot}/hold', [SlotController::class, 'hold']);
    Route::put('slots/{slot}/book', [SlotController::class, 'book']);
    Route::delete('slots/{slot}/hold', [SlotController::class, 'unhold']);
});