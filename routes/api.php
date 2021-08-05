<?php

use Illuminate\Support\Facades\Route;
use Symfony\Component\Process\Process;

Route::post('auth/login', 'AuthController@login');
Route::post('auth/recoverPassword', 'AuthController@recoverPassword');
Route::get('auth/me', 'AuthController@me');
Route::post('auth/newaccount', 'AuthController@newAccount');

Route::get('/git', function () {
	try {
		echo exec("cd /var/www/html/cashmanager && git pull origin master");
	} catch (\Throwable $th) {
		return response($th->getMessage());
	}
});
Route::post('/git', function () {
	$root_path = base_path();
	try {
		echo exec("cd /var/www/html/cashmanager && git pull origin master");
	} catch (\Throwable $th) {
		return response($th->getMessage());
	}

});

Route::group(['middleware' => 'apiJwt'], function () {
    Route::post('auth/logout', 'AuthController@logout');
    Route::post('auth/refresh', 'AuthController@refresh');
    Route::post('auth/validate', 'AuthController@tokenIsValidate');
    Route::put('auth/changePassword', 'AuthController@changePassword');

    Route::post('categories', 'CategoryController@store');
    Route::get('categories/{id}', 'CategoryController@show');
    Route::delete('categories/{id}', 'CategoryController@destroy');
    Route::get('categories', 'CategoryController@index');
    Route::put('categories', 'CategoryController@update');

    Route::post('accounts', 'AccountController@store');
    Route::get('accounts/{id}', 'AccountController@show');
    Route::delete('accounts/{id}', 'AccountController@destroy');
    Route::get('accounts', 'AccountController@index');
    Route::put('accounts', 'AccountController@update');

    Route::post('credit-card', 'CreditCardController@store');
    Route::get('credit-card/{id}', 'CreditCardController@show');
    Route::delete('credit-card/{id}', 'CreditCardController@destroy');
    Route::get('credit-card', 'CreditCardController@index');
    Route::put('credit-card', 'CreditCardController@update');

    Route::post('transactions', 'TransactionController@store');
    Route::get('transactions/{id}', 'TransactionController@show');
    Route::delete('transactions/{id}', 'TransactionController@destroy');
    Route::get('transactions', 'TransactionController@index');
    Route::put('transactions', 'TransactionController@update');

    Route::get('planned-expenses', 'PlannedExpensesController@index');
    Route::put('planned-expenses/{id}', 'PlannedExpensesController@update');
    Route::post('planned-expenses', 'PlannedExpensesController@store');
    Route::get('dashboard', 'DashboardController@index');
});
