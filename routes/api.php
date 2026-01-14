<?php

use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\ImageGenerationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware(['auth:sanctum', 'throttle:api'])->group(function(){
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('v1')->group(function(){
        Route::apiResource('posts', PostController::class);

        Route::apiResource('image-generations', ImageGenerationController::class)->only(['index', 'store']);
    });
});

require __DIR__.'/auth.php';

