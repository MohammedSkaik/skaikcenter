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
    Route::group(['middleware' => ['role:super_admin|center_admin']], function () {
        Route::apiResource('roles', RoleController::class);
        Route::get('permissions', PermissionController::class);
        Route::apiResource('managers', ManagerController::class);

        // Phase 2: Academic Controls
        Route::apiResource('academic-years', AcademicController::class);
        Route::post('academic-years/{academicYear}/semesters', [AcademicController::class, 'storeSemester']);
        Route::put('semesters/{semester}', [AcademicController::class, 'updateSemester']);
        Route::delete('semesters/{semester}', [AcademicController::class, 'destroySemester']);

        Route::apiResource('grades', StructureController::class);
        Route::post('grades/{grade}/subjects', [StructureController::class, 'storeSubject']);
        Route::put('subjects/{subject}', [StructureController::class, 'updateSubject']);
        Route::delete('subjects/{subject}', [StructureController::class, 'destroySubject']);

        Route::apiResource('rooms', RoomController::class);
        Route::apiResource('study-classes', StudyClassController::class);
    });

});
