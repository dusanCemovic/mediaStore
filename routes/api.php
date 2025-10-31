<?php

use App\Http\Middleware\ApiTokenMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MediaController;

Route::post('/upload-media', [MediaController::class, 'store']);
Route::get('/list', [MediaController::class, 'list']);
Route::get('/ping', [MediaController::class, 'ping'])
    ->withoutMiddleware(apiTokenMiddleware::class);
