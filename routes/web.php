<?php

use App\Http\Controllers\v1\Objects\ObjectController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        /** @var User $record */
        $user = Auth::user();
        $objects = $user->objects;

        return Inertia::render('dashboard', [
            'objects' => $objects,
        ]);
    })->name('dashboard');
});

// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/objects', [ObjectController::class, 'index']);
// });

// Route::get('dashboard', function () {
//     $objects = Auth::user()->objects; // предполагая, что есть отношение в модели User
//
//     return Inertia::render('Dashboard', [
//         'objects' => $objects,
//     ]);
// })->middleware(['auth', 'verified']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
