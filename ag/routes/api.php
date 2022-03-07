<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("connection",'API\ApiController@connection');
Route::post("website",'API\ApiController@updateWebsite');
Route::get("fancy/matches",'API\ApiController@getFancyMatches');
Route::get("match/fancy/{id}",'API\ApiController@getFancy');
Route::get("fancy/matches/history",'API\ApiController@getFancyMatchHistory');
Route::get("fancy/history/{id}",'API\ApiController@getFancyHistory');
Route::post("declare/fancy/result",'API\ApiController@declareFancyResult');
Route::post("rollback/fancy/result",'API\ApiController@rollbackFancyResult');
