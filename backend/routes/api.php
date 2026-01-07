<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    RoleController,
    PermissionController,
    ManagerController,
    AcademicController,
    EducationStageController, // Renamed from StructureController
    SubjectController, // New
    RoomController,
    StudyClassController,
    GuardianController,
    AuthController
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

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

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

        // Subjects (Global)
        Route::apiResource('subjects', SubjectController::class);

        // Education Stages (Grades)
        Route::apiResource('grades', EducationStageController::class);
        // Pivot: Attach/Detach Subject to Grade
        Route::post('grades/{grade}/subjects', [EducationStageController::class, 'storeSubject']); // Attach
        Route::put('grades/{grade}/subjects/{subject}', [EducationStageController::class, 'updateSubject']); // Update Pivot
        Route::delete('grades/{grade}/subjects/{subject}', [EducationStageController::class, 'destroySubject']); // Detach

        Route::apiResource('rooms', RoomController::class);
        Route::apiResource('study-classes', StudyClassController::class);

        // Guardians & Students (Phase 3)
        Route::apiResource('guardians', GuardianController::class);
    });

});
