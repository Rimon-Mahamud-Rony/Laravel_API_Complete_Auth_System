<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordResetController;

/*
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

//general routes -----------------------

//Route::post('register', [UserController::class, 'register']);
Route::post('register', [UserController::class, 'register']);

Route::post('login', [UserController::class, 'login']);

Route::post('reset_pass_email', [PasswordResetController::class, 'reset_pass_email']);

Route::post('reset_pass_with_token/{token}', [PasswordResetController::class, 'reset_pass_with_token']);


//



//Authenticated Routes---------------------------------

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('logout', [UserController::class, 'logout']);

    Route::get('/active_user', [UserController::class, 'active_user']);

    Route::post('/change_pass', [UserController::class, 'change_pass']);
});
