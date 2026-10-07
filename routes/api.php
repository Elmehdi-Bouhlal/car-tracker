<?php

use App\Http\Controllers\TruckerController;
use Illuminate\Support\Facades\Route;

Route::prefix('/truckers')->group(function () {
    Route::post('/', [TruckerController::class, 'store'])->name('truckers.store');
    Route::get('/{ident}/history', [TruckerController::class, 'history'])->name('truckers.history');
    Route::get('/{ident}', [TruckerController::class, 'show'])->name('truckers.show');
    Route::patch('/{ident}', [TruckerController::class, 'update'])->name('truckers.update');
});
