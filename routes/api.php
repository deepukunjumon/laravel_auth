<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AuthController;

//Common Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);

//User Routes
Route::group(['prefix' => 'user', 'middleware' => 'auth'], function () {
    Route::get('/dashboard', [UsersController::class, 'userDashboard']);
});


//Admin Routes
Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
    Route::get('/users/status/counts', [UsersController::class, 'getUsersStatusCounts']);
    Route::get('/dashboard', [UsersController::class, 'adminDashboard']);
    Route::get('/users', [UsersController::class, 'getUsers']);
    Route::get('/get/user/{id}', [UsersController::class, 'getUserDetails']);
    Route::get('/admin/users/profile/{id}', [UsersController::class, 'getUserDetails']);
    Route::post('/update/user/{id}', [UsersController::class, 'updateUserDetails']);
    Route::post('/change/user/role', [UsersController::class, 'changeUserRole']);
    Route::post('/delete/user/{id}', [UsersController::class, 'deleteUser']);
    Route::post('/disable/user/{id}', [UsersController::class, 'disableUser']);
    Route::post('/enable/user/{id}', [UsersController::class, 'enableUser']);
    Route::post('/add/user', [UsersController::class, 'addUser']);
});

