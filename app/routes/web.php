<?php

use App\Http\Controllers\DisplayController;
use App\Http\Controllers\RegistrationController;

/*
Route::get('/', function () {
    return view('welcome');
});
*/
Route::get('/dashboard', [DisplayController::class, 'index'])/*->middleware('auth')*/;
Route::get('/journals', [DisplayController::class, 'journals_index']);
Route::get('/qas', [DisplayController::class, 'qas_index']);
Route::get('/materials', [DisplayController::class, 'materials_index']);

Route::get('/journals/index', [DisplayController::class, 'journals_index']);
Route::get('/qas/index', [DisplayController::class, 'qas_index']);
Route::get('/materials/index', [DisplayController::class, 'materials_index']);

Route::post('/journals/store', [RegistrationController::class, 'storeJournal']);
Route::post('/qas/store', [RegistrationController::class, 'storeQa']);
Route::post('/materials/store', [RegistrationController::class, 'storeMaterial'])->middleware('auth');

Route::get('/journals', [DisplayController::class, 'journals']);
Route::get('/journals/weekly-data', [DisplayController::class, 'weeklyData']);
Route::post('/journals/store', [RegistrationController::class, 'storeJournal']);