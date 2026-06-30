<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FuzzyResourceController;

Route::get('/fuzzy', [FuzzyResourceController::class, 'index']);

Route::post('/fuzzy/calculate', [FuzzyResourceController::class, 'calculate']);