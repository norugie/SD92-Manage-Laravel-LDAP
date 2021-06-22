<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\GroupController;
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
    return view('welcome');
});

Route::get('/error', function () {
    return view('error');
});

Route::get('/restricted', function () {
    return view('restricted');
});

Route::get('/signin', [AuthController::class, 'signin']);
Route::get('/callback', [AuthController::class, 'callback']);
Route::get('/signout', [AuthController::class, 'signout']);

Route::group(['middleware' => 'authAD', 'prefix' => 'cms'], function (){
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::group(['prefix' => 'employees'], function (){
        // Employee Index
        Route::get('/', [EmployeeController::class, 'index']);

        // Employee - Create
        Route::get('/create', [EmployeeController::class, 'createEmployeeForm']);
        Route::post('/create', [EmployeeController::class, 'createEmployee']);

        // Employee View, Update, Disable
        Route::get('/{username}', function ( String $username ) { return redirect('/cms/employees/' . $username . '/view'); });
        Route::get('/{username}/disable', [EmployeeController::class, 'disableEmployeeProfile']);
        Route::get('/{username}/{action}', [EmployeeController::class, 'viewEmployeeProfileUpdate']);
        Route::post('/{username}/update', [EmployeeController::class, 'updateEmployeeProfile']);
    });

    Route::group(['prefix' => 'groups'], function (){
        // Group Index
        Route::get('/', [GroupController::class, 'index']);

        // Group View, Update
        Route::get('/{groupname}', function ( String $groupname ) { return redirect('/cms/groups/' . $groupname . '/view'); });
        Route::get('/{groupname}/{action}', [GroupController::class, 'viewGroupProfileUpdate']);
        Route::post('/{groupname}/update', [GroupController::class, 'updateGroupProfile']);
    });

    Route::group(['prefix' => 'inactive'], function (){
        // Inactive Index
        Route::get('/', [InactiveController::class, 'index']);

        // Inactive Enable
        Route::get('/{username}', function () { return redirect('/cms/inactive/'); });
        Route::get('/{username}/enable', [InactiveController::class, 'enableInactiveProfile']);
    });
});
