<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\appContoller;
use App\Http\Controllers\ikraController;
use App\Http\Controllers\datasetController;



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

Route::get('/', [appContoller::class, 'index']);
Route::get('/alladdress',[appContoller::class, 'all_locations']);
Route::get('/searchlocations/{location}',[appContoller::class, 'searchLocations']);
Route::post('/weatherdata',[appContoller::class, 'weatherdata']);
Route::post('/liveweather',[appContoller::class, 'liveweather']);
Route::post('/geolocate',[appContoller::class, 'geolocate']);



// protected routes*********************************************************************
Route::post('/ikra/register',  [ikraController::class, 'register']);
Route::post('/ikra/login', [ikraController::class, 'login']);

Route::post('/dataset', [datasetController::class, 'getToken']);



Route::middleware('auth:ikraUsers')->group(function () {
    // admin***************************************************
    Route::get('/ikra', [ikraController::class, 'index']);
    Route::get('/ikra/logout', [ikraController::class, 'logout']);
    Route::post('/ikra/user/update/{id}',  [ikraController::class, 'update']);
    Route::get('/ikra/user/delete/{id}',  [ikraController::class, 'delete']);

    Route::post('/contact', [ikraController::class, 'storeContact']);
    Route::get('/contact/show', [ikraController::class, 'showContacts']);
    Route::post('/contact/update/{id}', [ikraController::class, 'updateContact']);
    Route::post('/contact/delete/{id}', [ikraController::class, 'destroyContact']);
    Route::get('/ikra/notify', [ikraController::class, 'notify']);

    
});