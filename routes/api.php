<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\TagController;
use \App\Http\Controllers\Api\CategoryController;
use \App\Http\Controllers\Api\AdvertisementController;

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});


Route::get('/tags',[TagController::class,'index']);
Route::post('/tags',[TagController::class,'store']);
Route::post('/tags/update/{tag}',[TagController::class,'update']);
Route::post('/tags/destroy/{tag}',[TagController::class,'destroy']);




Route::get('/categories',[CategoryController::class,'index']);
Route::post('/categories',[CategoryController::class,'store']);
Route::post('/categories/update/{category}',[CategoryController::class,'update']);
Route::post('/categories/destroy/{category}',[CategoryController::class,'destroy']);


Route::get('/advertisements',[AdvertisementController::class,'index']);
Route::post('/advertisements',[AdvertisementController::class,'store']);
Route::get('/advertisements/show/{category}',[AdvertisementController::class,'show']);
Route::post('/advertisements/update/{category}',[AdvertisementController::class,'update']);
Route::post('/advertisements/destroy/{category}',[AdvertisementController::class,'destroy']);
