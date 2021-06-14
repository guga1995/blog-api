<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
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

Route::post('login', [AuthController::class, 'login']);
Route::post('posts/{post}/comments', [PostController::class, 'storeComment']);
Route::post('comments/{comment}/comments', [CommentController::class, 'storeComment']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('users/{user}/posts', [UserController::class, 'storePost']);
    Route::get('user', [UserController::class, 'showAuth']);

    Route::post('posts/{post}/files', [PostController::class, 'storeFile']);

    Route::post('logout', [AuthController::class, 'logout']);
});
