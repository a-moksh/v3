<?php

use App\Http\Controllers\Api\V1\AdminController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::get('users', 'Api\V1\AdminController@index');
    Route::post('login', [AdminController::class, 'login'])
        ->name('v1.rs.login');
    Route::group(
        ['middleware' => ['auth:admin']],
        function () {
            Route::post('logout', [AdminController::class, 'logout'])->name('v1.rs.logout');
            Route::get('me', [AdminController::class, 'me'])->name('v1.rs.me');
            Route::post('password', [AdminController::class, 'password'])->name('v1.rs.password');
        }
    );
});
