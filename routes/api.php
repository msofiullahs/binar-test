<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\EnsureUserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/users', [UserController::class, 'getUsers'])->name('userList');
Route::post('/user', [UserController::class, 'getUsers'])->middleware(['auth:sanctum', EnsureUserRole::class])->name('userList');
