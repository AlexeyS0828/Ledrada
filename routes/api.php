<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;


Route::get('screens', [\App\Http\Controllers\ScreensController::class, 'index']);
Route::get('screens/{screenId}', [\App\Http\Controllers\ScreensController::class, 'getQuota']);
Route::post('video', [\App\Http\Controllers\VideoController::class, 'upload']);
Route::post('campaigns', [\App\Http\Controllers\CampaignController::class, 'create']);
Route::get('campaigns/{email}/{code}/paymentStatus', [\App\Http\Controllers\CampaignController::class, 'getPaymentStatus']);
Route::get('campaigns/{email}/{code}/payment', [\App\Http\Controllers\CampaignController::class, 'showAvailableCrypto']);
Route::post('campaigns/{email}/{code}/payment', [\App\Http\Controllers\CampaignController::class, 'createPayment']);
Route::post('campaigns/{email}/{code}/paymentIpn', [\App\Http\Controllers\CampaignController::class, 'paymentIpn'])
    ->name('campaignPaymentIpn');
Route::get('campaigns/{email}/{code}', [\App\Http\Controllers\CampaignController::class, 'show']);
