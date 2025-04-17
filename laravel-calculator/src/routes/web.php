<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/calculator', [App\Http\Controllers\CalculatorController::class, 'index'])->name('calculator');

Route::post('/calculator/calculate', [App\Http\Controllers\CalculatorController::class, 'calculate'])->name('calculator.calculate');