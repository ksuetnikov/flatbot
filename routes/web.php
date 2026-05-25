<?php

use App\Http\Controllers\v1\Object\ObjectController;
use App\Http\Controllers\v1\Object\ObjectFinanceController;
use App\Http\Controllers\v1\Object\ObjectPropertyController;
use App\Http\Controllers\v1\Object\ObjectPropertyFileController;
use App\Http\Controllers\v1\Object\PropertyReadingController;
use App\Http\Controllers\v1\SubscribeUser\SubscribeUserController;
use App\Models\User;
use App\Services\Object\ObjectFinanceSummaryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard route
    Route::get('dashboard', function (ObjectFinanceSummaryService $financeSummaryService) {
        $user = Auth::user();
        $objects = $user->objects;
        $summaries = $financeSummaryService->summariesForObjects($objects);

        return Inertia::render('dashboard', [
            'objects' => $objects->map(static fn ($object) => [
                'id' => $object->id,
                'name' => $object->name,
                'description' => $object->description,
                'notification_date' => $object->notification_date,
                'created_at' => $object->created_at,
                'updated_at' => $object->updated_at,
                'finance_summary' => $summaries[$object->id] ?? [
                    'total_income' => 0,
                    'total_expense' => 0,
                    'balance' => 0,
                    'entries_count' => 0,
                ],
            ]),
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
    Route::get('/objects/{object}/properties/{propertyId}/files/{fileId}', [ObjectPropertyFileController::class, 'show'])->name('property.files.show');
    Route::post('/objects/{object}/properties/{propertyId}/files', [ObjectPropertyFileController::class, 'store'])->name('property.files.store');
    Route::put('/objects/{object}/properties/{propertyId}/files/{fileId}', [ObjectPropertyFileController::class, 'update'])->name('property.files.update');
    Route::delete('/objects/{object}/properties/{propertyId}/files/{fileId}', [ObjectPropertyFileController::class, 'destroy'])->name('property.files.destroy');
    Route::get('/objects/{object}/properties/{propertyId}/readings/export', [PropertyReadingController::class, 'export'])->name('property.readings.export');
    Route::post('/objects/{object}/properties/{propertyId}/readings', [PropertyReadingController::class, 'store'])->name('property.readings.store');
    Route::put('/objects/{object}/properties/{propertyId}/readings/{readingId}', [PropertyReadingController::class, 'update'])->name('property.readings.update');
    Route::post('/objects/{object}/properties', [ObjectPropertyController::class, 'create'])->name('property.create');
    Route::put('/objects/{object}/properties/{propertyId}', [ObjectPropertyController::class, 'update'])->name('property.update');
    Route::delete('/objects/{object}/properties/{propertyId}', [ObjectPropertyController::class, 'destroy'])->name('property.destroy');

    // Finances
    Route::get('/objects/{object}/finances/export', [ObjectFinanceController::class, 'export'])->name('object.finances.export');
    Route::get('/objects/{object}/finances', [ObjectFinanceController::class, 'show'])->name('object.finances');
    Route::post('/objects/{object}/finances', [ObjectFinanceController::class, 'store'])->name('object.finances.store');
    Route::delete('/objects/{object}/finances/{entryId}', [ObjectFinanceController::class, 'destroy'])->name('object.finances.destroy');

});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
