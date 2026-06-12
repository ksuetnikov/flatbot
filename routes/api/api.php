<?php

use App\Http\Controllers\v1\Meter\MeterController;
use Illuminate\Support\Facades\Route;

Route::post('/meters/', [MeterController::class, 'create'])->name('meter.create');
Route::post('/meters/{readingId}/photo', [MeterController::class, 'uploadPhoto'])->name('meter.photo');

