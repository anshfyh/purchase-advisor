<?php

use App\Http\Controllers\Api\FuzzyResourceController;
use Illuminate\Support\Facades\Route;

Route::get('/fuzzy', [FuzzyResourceController::class, 'index']);
Route::post('/fuzzy/calculate', [FuzzyResourceController::class, 'calculate']);
