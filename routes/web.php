<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ViewEmployeeController;
use App\Http\Controllers\ViewStudentController;
use App\Http\Controllers\CreateEmployeeController;
use App\Http\Controllers\UpdateEmployeeController;
use App\Http\Controllers\DisableEmployeeController;
use App\Http\Controllers\EnableEmployeeController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Web routes for the SD92 User Manager web app. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
| Please refrain from rearranging the order of the routes as it
| could affect some of the functionalities.
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

Route::group(['middleware' => 'authAD', 'prefix' => 'cms'], function (){
    // View Dashboard - Log Index
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
            // Update Employee - Profile ID
            Route::post('/{username}/update/image/{userID}', 'updateEmployeeProfileIDImage');
        });

        Route::controller('DisableEmployeeController')->group(function (){
            // Disable Employee - Disable Multiple Accounts
            Route::post('/disable', 'disableEmployeeMultiple');
            // Disable Employee - Profile
            Route::get('/{username}/disable', 'disableEmployeeProfile');
        });

        Route::controller('ViewEmployeeController')->group(function (){
            // View Employee - Active Employee Index
            Route::get('/', 'enabledEmployeeAccountsIndex');
            // View Employee - Create Employee
            Route::get('/create', 'createEmployeeForm');
            // View Employee - Profile
            Route::get('/{username}/{action}', 'viewEmployeeProfileUpdate');
            // View Employee - Profile ID Download
            Route::get('/{username}/download/image', 'viewEmployeeProfileIDImageDownload');
        });
    });

    Route::group(['prefix' => 'inactive'], function (){
        // Inactive Employee - Inactive Employee Index
        Route::get('/', [ViewEmployeeController::class, 'disabledEmployeeAccountsIndex']);

        // View Inactive Employee Reroute
        Route::get('/{username}', function () { return redirect('/cms/inactive/'); });

        Route::controller('EnableEmployeeController')->group(function (){
            // Enable Employee - Enable Multiple Accounts
            Route::post('/enable', 'enableInactiveMultiple');
            // Enable Employee - Profile
            Route::get('/{username}/enable', 'enableInactiveProfile');
        });
    });

    Route::group(['prefix' => 'students'], function(){
        // View Student - Active Student Index
        Route::get('/', [ViewStudentController::class, 'enabledStudentAccountsIndex']);
    });
});
