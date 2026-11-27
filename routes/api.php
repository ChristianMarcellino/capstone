<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WhatsappDraftController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);

// Guest accessible
Route::get('/products', [ProductController::class, 'index']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Admin only
Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
    Route::apiResource('products', ProductController::class)->except('index');
    Route::apiResource('categories', CategoryController::class)->except('index');
    Route::apiResource('events', EventController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('whatsapp-drafts', WhatsappDraftController::class)->parameters(['whatsapp-drafts' => 'whatsappDraft']); //Ensuring id parameter name is consistent
});
