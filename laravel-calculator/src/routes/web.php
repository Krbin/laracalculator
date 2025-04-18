<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalculatorController;

Route::get('/', [CalculatorController::class, 'index'])->name('calculator.index');

Route::get('/calculate', [CalculatorController::class, 'calculate'])->name('calculator.calculate');
