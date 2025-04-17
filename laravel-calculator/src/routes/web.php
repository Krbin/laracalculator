<?php
// routes/web.php

use App\Http\Controllers\CalculatorController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CalculatorController::class, 'index'])->name('calculator');

Route::get('/evaluate', [CalculatorController::class, 'evaluate'])->name('calculator.evaluate');
