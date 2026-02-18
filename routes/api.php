<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobListController;

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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
   
});
Route::get('department',[JobListController::class,'Department']);
Route::post('store_dep',[JobListController::class,'store_department']);
Route::delete('Department_delete/{id}',[JobListController::class,'Department_delete']);

Route::post('store_position',[JobListController::class,'store_position']);
Route::get('position',[JobListController::class,'position']);
Route::delete('position_delete/{id}',[JobListController::class,'position_delete']);



// ✅ LOAD BREEZE AUTH ROUTES
require __DIR__ . '/auth.php';