<?php

use Illuminate\Support\Facades\Route;
use Symfony\Component\Process\Process;

Route::post('auth/login', 'AuthController@login');
Route::post('auth/recoverPassword', 'AuthController@recoverPassword');
Route::get('auth/me', 'AuthController@me');
Route::post('auth/newaccount', 'AuthController@newAccount');

Route::get('/git', function () {
	echo exec("git pull");


    // $process = new Process(["./deploy.sh"]);
    // $process->run(function ($type, $buffer) {
    //     echo $buffer;

    //     return response($buffer);
    // });
});

Route::group(['middleware' => 'apiJwt'], function () {
    Route::post('auth/logout', 'AuthController@logout');
    Route::post('auth/refresh', 'AuthController@refresh');
    Route::post('auth/validate', 'AuthController@tokenIsValidate');
    Route::put('auth/changePassword', 'AuthController@changePassword');
    Route::resource('categories', 'CategoryController');
    Route::resource('accounts', 'AccountController');
    Route::resource('credit-card', 'CreditCardController');
    Route::resource('transactions', 'TransactionController');
    Route::get('planned-expenses', 'PlannedExpensesController@index');
    Route::put('planned-expenses/{id}', 'PlannedExpensesController@update');
    Route::post('planned-expenses', 'PlannedExpensesController@store');
    Route::get('dashboard', 'DashboardController@index');
});
