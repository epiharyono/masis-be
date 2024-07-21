<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\RouteController as AdminRoute;
use App\Http\Controllers\Api\RouteController as ApiRoute;
use App\Http\Controllers\Bernet\RouteController as BernetRoute;
use App\Http\Controllers\Wbs\RouteController as WbsRoute;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', function () {
    return [
        'success' => true,
        'message' => 'Server Asis Mobile BE Running',
    ];
});

Route::group(['prefix'=>'admin', 'middleware'=>'jwt.verify'], function() {
    Route::get('/',[AdminRoute::class,'index']);
    Route::get('/{satu}',[AdminRoute::class,'IndexRouteSatu']);
    Route::get('/{satu}/{dua}',[AdminRoute::class,'IndexRouteDua']);
    Route::get('/{satu}/{dua}/{tiga}',[AdminRoute::class,'IndexRouteTiga']);

    Route::post('/',[AdminRoute::class,'index']);
    Route::post('/{satu}',[AdminRoute::class,'IndexRouteSatu']);
    Route::post('/{satu}/{dua}',[AdminRoute::class,'IndexRouteDua']);
    Route::post('/{satu}/{dua}/{tiga}',[AdminRoute::class,'IndexRouteTiga']);
});

Route::group(['prefix'=>'web'], function() {
    Route::get('/',[ApiRoute::class,'index']);
    Route::get('/{satu}',[ApiRoute::class,'IndexRouteSatu']);
    Route::get('/{satu}/{dua}',[ApiRoute::class,'IndexRouteDua']);
    Route::get('/{satu}/{dua}/{tiga}',[ApiRoute::class,'IndexRouteTiga']);

    Route::post('/',[ApiRoute::class,'index']);
    Route::post('/{satu}',[ApiRoute::class,'IndexRouteSatu']);
    Route::post('/{satu}/{dua}',[ApiRoute::class,'IndexRouteDua']);
    Route::post('/{satu}/{dua}/{tiga}',[ApiRoute::class,'IndexRouteTiga']);
});

Route::group(['prefix'=>'bernet'], function() {
    Route::get('/',[BernetRoute::class,'index']);
    Route::get('/{satu}',[BernetRoute::class,'IndexRouteSatu']);
    Route::get('/{satu}/{dua}',[BernetRoute::class,'IndexRouteDua']);
    Route::get('/{satu}/{dua}/{tiga}',[BernetRoute::class,'IndexRouteTiga']);

    Route::post('/',[BernetRoute::class,'index']);
    Route::post('/{satu}',[BernetRoute::class,'IndexRouteSatu']);
    Route::post('/{satu}/{dua}',[BernetRoute::class,'IndexRouteDua']);
    Route::post('/{satu}/{dua}/{tiga}',[BernetRoute::class,'IndexRouteTiga']);
});

Route::group(['prefix'=>'api-wbs' ], function() {
    Route::get('/',[WbsRoute::class,'index']);
    Route::get('/{satu}',[WbsRoute::class,'IndexRouteSatu']);
    Route::get('/{satu}/{dua}',[WbsRoute::class,'IndexRouteDua']);
    Route::get('/{satu}/{dua}/{tiga}',[WbsRoute::class,'IndexRouteTiga']);

    Route::post('/',[WbsRoute::class,'index']);
    Route::post('/{satu}',[WbsRoute::class,'IndexRouteSatu']);
    Route::post('/{satu}/{dua}',[WbsRoute::class,'IndexRouteDua']);
    Route::post('/{satu}/{dua}/{tiga}',[WbsRoute::class,'IndexRouteTiga']);
});
