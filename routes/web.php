<?php

use App\Http\Controllers\TruckerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TruckerController::class, 'index'])->name('home');
