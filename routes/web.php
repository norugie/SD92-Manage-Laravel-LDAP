<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HelperEmployeeController;
use App\Http\Controllers\ViewEmployeeController;
use App\Http\Controllers\CreateEmployeeController;
use App\Http\Controllers\UpdateEmployeeController;
use App\Http\Controllers\DisableEmployeeController;
use App\Http\Controllers\EnableEmployeeController;
use App\Http\Controllers\InactiveController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/cms/dashboard');
})->middleware('authAD');

Route::get('/error', function () {
    return view('error');
});

Route::get('/restricted', function () {
    return view('restricted');
});

Route::get('/signin', [AuthController::class, 'signin']);
Route::get('/callback', [AuthController::class, 'callback']);
Route::get('/signout', [AuthController::class, 'signout']);

// Test
Route::get('/test', [ViewEmployeeController::class, 'test']);

// Route::get('/test', [EmployeeController::class, 'setEmployeeID']);

Route::group(['middleware' => 'authAD', 'prefix' => 'cms'], function (){
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::group(['prefix' => 'employees'], function (){
        // Create Employee - Profile
        Route::post('/create', [CreateEmployeeController::class, 'createEmployee']);

        // View Employee Reroute
        Route::get('/{username}', function ( String $username ) { return redirect('/cms/employees/' . $username . '/view'); });

        Route::controller('UpdateEmployeeController')->group(function (){
            // Update Employee - Move Multiple Accounts
            Route::post('/update', 'updateEmployeeRolesMultiple');
            // Update Employee - Profile
            Route::post('/{username}/update', 'updateEmployeeProfile');
        });

        Route::controller('DisableEmployeeController')->group(function (){
            // Disable Employee - Disable Multiple Accounts
            Route::post('/disable', 'disableEmployeeMultiple');
            // Disable Employee - Profile
            Route::get('/{username}/disable', 'disableEmployeeProfile');
        });

        Route::controller('ViewEmployeeController')->group(function (){
            // View Employee - Index
            Route::get('/', 'enabledEmployeeAccountsIndex');
            // View Employee - Create
            Route::get('/create', 'createEmployeeForm');
            // View Employee - Profile
            Route::get('/{username}/{action}', 'viewEmployeeProfileUpdate');
        });
    });

    Route::group(['prefix' => 'inactive'], function (){
        // Inactive Employee - Index
        Route::get('/', [ViewEmployeeController::class, 'disabledEmployeeAccountsIndex']);

        // View Inactive Employee Reroute
        Route::get('/{username}', function () { return redirect('/cms/inactive/'); });

        Route::controller('EnableEmployeeController')->group(function (){
            // Employee - Disable Multiple Accounts
            Route::post('/enable', 'enableInactiveMultiple');
            // Inactive Enable
            Route::get('/{username}/enable', 'enableInactiveProfile');
        });
    });
});
