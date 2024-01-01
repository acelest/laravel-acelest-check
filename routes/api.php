<?php

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperviseurController;
use App\Http\Controllers\AdminController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('user/absences', [UserController::class, 'viewAbsences']);
    Route::post('user/send-message-to-admin', [UserController::class, 'sendMessageToAdmin']);
});

Route::middleware(['auth:sanctum', 'role:superviseur'])->group(function () {
    Route::get('superviseur/list-users', [SuperviseurController::class, 'listUsers']);
    Route::get('superviseur/list-absences/{userId}', [SuperviseurController::class, 'listAbsencesPerUser']);
    Route::get('superviseur/view-motifs/{absenceId}', [SuperviseurController::class, 'viewMotifsForAbsence']);
    Route::post('superviseur/authorize-delete-absence/{absenceId}', [SuperviseurController::class, 'authorizeDeleteAbsence']);
    Route::post('superviseur/cancel-absence/{absenceId}', [SuperviseurController::class, 'cancelAbsence']);
    Route::post('superviseur/logout', [SuperviseurController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::post('create-user', [AdminController::class, 'createUser']);
        Route::put('update-user/{userId}', [AdminController::class, 'updateUser']);
        Route::delete('delete-user/{userId}', [AdminController::class, 'deleteUser']);
        Route::get('list-users', [AdminController::class, 'listUsers']);

        Route::post('mark-absent-users', [AdminController::class, 'markAbsentUsers']);
        Route::post('send-notification-to-superviseur', [AdminController::class, 'sendNotificationToSuperviseur']);
        Route::post('send-notification-to-absent-user/{userId}', [AdminController::class, 'sendNotificationToAbsentUser']);
        Route::post('cancel-absence/{absenceId}', [AdminController::class, 'cancelAbsenceAfterAuthorization']);
        Route::get('list-monthly-absences/{userId}', [AdminController::class, 'listMonthlyAbsences']);
        Route::get('print-member-card/{userId}', [AdminController::class, 'printMemberCard']);
    });
});
