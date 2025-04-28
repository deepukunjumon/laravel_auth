<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

//Common Routes
Route::post('/register',[UsersController::class, 'register']);
Route::post('/login',[UsersController::class, 'login']);
Route::post('/logout',[UsersController::class, 'logout']);

//User Routes
Route::group(['prefix' => 'user', 'middleware' => 'auth'], function () {
    Route::get('/dashboard',[UsersController::class, 'userDashboard']);
});


//Admin Routes
Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
    Route::get('/users/status/counts',[UsersController::class, 'getUsersStatusCounts']);
    Route::get('/dashboard',[UsersController::class, 'adminDashboard']);
    Route::get('/users', [UsersController::class, 'getUsers']);
    Route::get('/get/user/{id}',[UsersController::class, 'getUserDetails']);
    Route::post('/update/user/{id}',[UsersController::class, 'updateUserDetails']);
    Route::post('/change/user/role', [UsersController::class, 'changeUserRole']);
    Route::post('/user/delete', [UsersController::class, 'deleteUser']);
    Route::post('/add/user', [UsersController::class, 'addUser']);
});
