<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\LectureController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    Route::apiResource('students', StudentController::class);
    Route::post('students/{student}/attach-group/{group}', [StudentController::class, 'attachGroup']);
    Route::post('students/{student}/detach-group', [StudentController::class, 'detachGroup']);

    Route::apiResource('groups', GroupController::class);
    Route::get('groups/{group}/curriculum', [GroupController::class, 'curriculum']);
    Route::put('groups/{group}/curriculum', [GroupController::class, 'updateCurriculum']);

    Route::apiResource('lectures', LectureController::class);
    Route::get('lectures/group/{group}', [LectureController::class, 'byGroup']);
    Route::post('lectures/{lecture}/attend/{student}', [LectureController::class, 'markAttendance']);
});
