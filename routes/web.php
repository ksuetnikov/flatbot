<?php

use App\Http\Controllers\v1\Object\ObjectController;
use App\Http\Controllers\v1\Object\ObjectPropertyController;
use App\Http\Controllers\v1\Object\PropertyReadingController;
use App\Http\Controllers\v1\SubscribeUser\SubscribeUserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard route
    Route::get('dashboard', function () {
        $user = Auth::user();
        $objects = $user->objects;

        return Inertia::render('dashboard', [
            'objects' => $objects,
        ]);
    })->name('dashboard');

    // Object routes
    Route::post('/objects', [ObjectController::class, 'create'])->name('object.create');
    Route::get('/objects/{object}', [ObjectController::class, 'show'])->name('object.show');
    Route::get('/objects/{object}/edit', [ObjectController::class, 'edit'])->name('object.edit');
    Route::put('/objects/{object}', [ObjectController::class, 'update'])->name('object.update');
    Route::delete('/objects/{object}', [ObjectController::class, 'destroy'])->name('object.destroy');

    //Subscribers
    Route::post('/objects/{object}/subscribers', [SubscribeUserController::class, 'create'])->name('subscriber.create');
    Route::put('/objects/{object}/subscribers/{subscriberId}', [SubscribeUserController::class, 'update'])->name('subscriber.update');
    Route::delete('/objects/{object}/subscribers/{subscriberId}', [SubscribeUserController::class, 'destroy'])->name('subscriber.destroy');

    //Object properties
    Route::get('/objects/{object}/properties/{propertyId}', [ObjectPropertyController::class, 'show'])->name('property.show');
    Route::post('/objects/{object}/properties/{propertyId}/readings', [PropertyReadingController::class, 'store'])->name('property.readings.store');
    Route::put('/objects/{object}/properties/{propertyId}/readings/{readingId}', [PropertyReadingController::class, 'update'])->name('property.readings.update');
    Route::post('/objects/{object}/properties', [ObjectPropertyController::class, 'create'])->name('property.create');
    Route::put('/objects/{object}/properties/{propertyId}', [ObjectPropertyController::class, 'update'])->name('property.update');
    Route::delete('/objects/{object}/properties/{propertyId}', [ObjectPropertyController::class, 'destroy'])->name('property.destroy');

});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
