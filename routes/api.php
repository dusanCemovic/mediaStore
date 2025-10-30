<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MediaController;

Route::post('/upload-media', [MediaController::class, 'store']);
