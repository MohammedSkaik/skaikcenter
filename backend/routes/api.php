<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    RoleController,
    PermissionController,
    ManagerController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Permission Management Routes
    // Ensure only Super Admin or capable Center Admin can access these
    Route::group(['middleware' => ['role:super_admin|center_admin']], function () {
        Route::apiResource('roles', RoleController::class);
        Route::get('permissions', PermissionController::class);
        Route::apiResource('managers', ManagerController::class);
    });

});
