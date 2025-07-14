<?php

use App\Http\Controllers\v1\Objects\ObjectController;
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
    Route::get('/objects/{object}', [ObjectController::class, 'show'])->name('object.show');
    Route::get('/objects/{object}/edit', [ObjectController::class, 'show'])->name('object.editWithInformation');
    Route::put('/objects/{object}', [ObjectController::class, 'update'])->name('object.update');
    Route::delete('/objects/{object}', [ObjectController::class, 'destroy'])->name('object.destroy');
});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
