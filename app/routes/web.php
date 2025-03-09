<?php

use App\Http\Controllers\DisplayController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 認証ルート
Auth::routes();

// 認証が必要なルート
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DisplayController::class, 'index']);

    // ✅ 生徒専用（学習ジャーナル）
    Route::middleware(['role:0'])->group(function () {
        Route::get('/journals', [DisplayController::class, 'journals_index'])->name('journals_index');
        Route::get('/journals/weekly-data', [DisplayController::class, 'weeklyData']);
        Route::post('/journals/store', [RegistrationController::class, 'storeJournal'])->name('journals.store');
    });

    // ✅ 教師専用（生徒管理）
    Route::middleware(['role:1'])->group(function () {
        Route::get('/students', [DisplayController::class, 'indexManagement'])->name('students.index');
        Route::get('/students/{id}/journals', [DisplayController::class, 'showStudentJournals'])->name('students.journals');
    });

    // ✅ 全ユーザー対象（Q&Aと教材）
    Route::get('/qas', [DisplayController::class, 'qas_index']);
    Route::get('/materials', [DisplayController::class, 'materials_index']);
    Route::post('/qas/store', [RegistrationController::class, 'storeQa'])->name('qas.store');
    Route::post('/materials/store', [RegistrationController::class, 'storeMaterial']);
});