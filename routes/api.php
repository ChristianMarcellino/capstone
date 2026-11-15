<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WhatsappDraftController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('products', ProductController::class);
Route::apiResource('events', EventController::class);
Route::apiResource('orders', OrderController::class);
Route::apiResource('whatsapp-drafts', WhatsappDraftController::class)->parameters(['whatsapp-drafts' => 'whatsappDraft']); //Ensuring id parameter name is consistent
