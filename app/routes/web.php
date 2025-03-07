<?php

use App\Http\Controllers\DisplayController;
use App\Http\Controllers\RegistrationController;

/*
Route::get('/', function () {
    return view('welcome');
});
*/

Route::get('/',[DisplayController::class, 'index']);
Route::get('/journals', [DisplayController::class, 'journals']);
Route::get('/qas', [DisplayController::class, 'qas']);
Route::get('/materials', [DisplayController::class, 'materials']);

Route::post('/journals/store', [RegistrationController::class, 'storeJournal']);
Route::post('/qas/store', [RegistrationController::class, 'storeQa']);
Route::post('/materials/store', [RegistrationController::class, 'storeMaterial']);