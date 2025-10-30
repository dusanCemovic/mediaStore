<?php

use App\Http\Middleware\ApiTokenMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MediaController;

Route::post('/upload-media', [MediaController::class, 'store']);
Route::get('/ping', [MediaController::class, 'list'])
    ->withoutMiddleware(apiTokenMiddleware::class);
