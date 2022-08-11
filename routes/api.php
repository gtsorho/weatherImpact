<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\appContoller;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', [appContoller::class, 'index']);
Route::get('/alladdress',[appContoller::class, 'all_locations']);
Route::post('/weatherdata',[appContoller::class, 'weatherdata']);
Route::post('/liveweather',[appContoller::class, 'liveweather']);
Route::post('/geolocate',[appContoller::class, 'geolocate']);


