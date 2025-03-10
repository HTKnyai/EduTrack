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
        Route::post('/materials/store', [RegistrationController::class, 'storeMaterial'])->name('materials.store');
        Route::put('/materials/{id}', [RegistrationController::class, 'updateMaterial'])->name('materials.update');
        Route::delete('/materials/{id}', [RegistrationController::class, 'destroyMaterial'])->name('materials.destroy');
        Route::get('/materials/download/{id}', [RegistrationController::class, 'downloadMaterial'])->name('materials.download'); // ✅ 追加
    });

    // ✅ 全ユーザー対象（Q&Aと教材）
    Route::get('/qas', [DisplayController::class, 'qas_index'])->name('qas_index');
    Route::post('/qas/store', [RegistrationController::class, 'storeQa'])->name('qas.store');
    Route::put('/qas/{id}', [RegistrationController::class, 'updateQa'])->name('qas.update');
    Route::delete('/qas/{id}', [RegistrationController::class, 'destroyQa'])->name('qas.destroy');

    Route::get('/materials', [DisplayController::class, 'materials_index'])->name('materials.index'); 
    Route::get('/materials/download/{id}', [RegistrationController::class, 'downloadMaterial'])->name('materials.download');
});