<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobListController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\SearchController;

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
Route::get('/positions/{id}', [JobListController::class, 'show_position']); 
Route::put('/position/{id}', [JobListController::class, 'update_position']);
Route::patch('/position/{id}', [JobListController::class, 'update_position']);
Route::delete('position_delete/{id}',[JobListController::class,'position_delete']);
Route::get('/dashboard-stats', [JobListController::class, 'dashboard_stats']);

Route::patch('/positions/{id}/toggle-status', [JobListController::class, 'toggle_position_status']);


Route::post('/apply', [CandidateController::class, 'store']);

// Admin routes
Route::get('/admin/candidates', [CandidateController::class, 'getCandidates']);
Route::post('/admin/candidates/{id}/schedule', [CandidateController::class, 'schedule']);
Route::post('/admin/candidates/{id}/accept', [CandidateController::class, 'accept']);
Route::post('/admin/candidates/{id}/reject', [CandidateController::class, 'reject']);
Route::post('/admin/candidates/bulk-reject', [CandidateController::class, 'bulkReject']);
Route::post('/admin/candidates/{id}/email', [CandidateController::class, 'sendEmail']);
Route::get('/admin/candidates/{id}/resume/download', [CandidateController::class, 'downloadResume']);
Route::get('/admin/candidates/{id}/resume/preview', [CandidateController::class, 'previewResume']);

// Add this with your other routes
Route::get('/search', [SearchController::class, 'search']);

// ✅ LOAD BREEZE AUTH ROUTES
require __DIR__ . '/auth.php';