<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\OfficeSpaceController;
use App\Http\Controllers\Api\BookingTransactionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('apikey')->group(function () {
    Route::get('/city/{city:slug}', [CityController::class, 'show']);
    Route::apiResource('/cities', CityController::class);
    Route::get('/office/{officeSpace:slug}', [OfficeSpaceController::class, 'show']);
    Route::apiResource('/offices', OfficeSpaceController::class);
    Route::post('/booking-transactions', [BookingTransactionController::class, 'store']);
    Route::get('/check-booking', [BookingTransactionController::class, 'booking_details']);
});
