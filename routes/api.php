<?php

use Illuminate\Support\Facades\Route;

Route::post('auth/login', 'AuthController@login');
Route::post('auth/recoverPassword', 'AuthController@recoverPassword');
Route::get('auth/me', 'AuthController@me');

Route::group(['middleware' => 'apiJwt'], function () {
    Route::post('auth/logout', 'AuthController@logout');
    Route::post('auth/refresh', 'AuthController@refresh');
    Route::post('auth/validate', 'AuthController@tokenIsValidate');
    Route::put('auth/changePassword', 'AuthController@changePassword');
});
