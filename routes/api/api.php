<?php

use App\Http\Controllers\v1\Object\ObjectController;
use App\Http\Controllers\v1\SubscribeUser\SubscribeUserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified'])->group(function () {

    Route::post('/objects/{objectId}/subscribers', [SubscribeUserController::class, 'create'])->name('subscriber.create');
    Route::delete('/objects/{objectId}/subscribers/{subscriberId}', [SubscribeUserController::class, 'destroy'])->name('subscriber.destroy');
});


require __DIR__.'/../settings.php';
require __DIR__.'/../auth.php';
