<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
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
        Route::get('/', [EmployeeController::class, 'index']);
        Route::get('/create', [EmployeeController::class, 'createEmployeeForm']);
        Route::get('/{username}', [EmployeeController::class, 'viewEmployeeProfile']);
        Route::get('/{username}/update', [EmployeeController::class, 'viewEmployeeProfileUpdateForm']);
        Route::post('/create', [EmployeeController::class, 'createEmployee']);
        Route::post('/{username}/update', [EmployeeController::class, 'updateEmployeeProfile']);
    });
});
