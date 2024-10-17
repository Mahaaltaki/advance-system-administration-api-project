<?php

use App\Http\Controllers\AttachmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;


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
Route::middleware(['checkStatus'])->group(function () {
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});
Route::get('/tasks',[TaskController::class,'index']);
Route::post('/tasks/{task}',[TaskController::class,'store']);
Route::post('/tasks/{id}',[TaskController::class,'show']);
Route::put('/tasks/{id}',[TaskController::class,'update']);
Route::delete('/tasks/{id}',[TaskController::class,'delete']);
Route::post('/tasks/{taskId}/reassign', [TaskController::class, 'reassignUser']);
Route::post('/tasks/{taskId}/assign', [TaskController::class, 'assignTask']);
Route::get('/tasks/{taskId}', [TaskController::class, 'show']);
Route::post('/tasks/{taskId}/comments', [TaskController::class, 'addComment']);
Route::post('/tasks/{taskId}/attachment', [TaskController::class, 'addAttachment']);
});
?>