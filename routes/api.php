<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\AuthenticateOptional;
use App\Http\Middleware\EnsureUserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/token', [UserController::class, 'getToken'])->name('userToken');
Route::get('/users', [UserController::class, 'getUsers'])->middleware(AuthenticateOptional::class)->name('userList');
Route::post('/user', [UserController::class, 'createUser'])->middleware(['auth:sanctum', EnsureUserRole::class])->name('addUser');
