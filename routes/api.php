<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\OfficeSpaceController;
use App\Http\Controllers\Api\BookingTransactionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/cities', CityController::class);
Route::apiResource('/office-spaces', OfficeSpaceController::class);
Route::apiResource('/booking-transactions', BookingTransactionController::class);
