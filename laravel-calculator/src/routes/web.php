<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\CalculatorController::class, 'index'])->name('calculator');

Route::post('/calculator/evaluate', [App\Http\Controllers\CalculatorController::class, 'evaluate'])->name('calculator.evaluate');