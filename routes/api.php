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
Route::apiResource('patient', \App\Http\Controllers\ApiPatientContrller::class, array("as" => "api"));
Route::get('suppliers_list', [\App\Http\Controllers\SupplierController::class, 'suppliers_list']);
Route::get('supplier_medicines/{id}', [\App\Http\Controllers\SupplierController::class, 'supplier_medicines']);
Route::get('suppliers_name', [\App\Http\Controllers\SupplierController::class, 'suppliers_name']);