<?php

use App\Http\Controllers\Api\V1\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\post;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/hello', function () {
    return ["message" => "Hello laravel API!"];
});

Route::prefix('v1')->group(function () {
    Route::apiResource('posts', PostController::class);
});
