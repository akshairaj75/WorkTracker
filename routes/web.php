<?php

use App\Http\Controllers\WorkstatusController;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login',[AuthController::class,'showLogin'])->name('login');
Route::post('/login',[AuthController::class,'login'])->name('login.post');
Route::get('/register',[AuthController::class,'showregister'])->name('register');
Route::post('/register',[AuthController::class,'register'])->name('register.post');

Route::middleware('auth')->group(function(){
    Route::post('/logout',[AuthController::class,'logout'])->name('logout');    
    Route::get('/',[WorkstatusController::class,'index'])->name('work.index');
    Route::get('/create',[WorkstatusController::class,'create'])->name('work.create');
    Route::get('/admin/manage',[WorkstatusController::class,'adminManage'])->name('work.adminManage');
    Route::post('/store',[WorkstatusController::class,'store'])->name('work.store');
    Route::get('/{id}/edit',[WorkstatusController::class,'edit'])->name('work.edit');
    Route::put('/{id}/updateEdit',[WorkstatusController::class,'updateEdit'])->name('work.updateEdit');

    Route::delete('/attachment/{id}/delete', [WorkstatusController::class, 'attachmentDestroy'])->name('attachment.delete');
    Route::get('/exportPdf',[WorkstatusController::class,'exportPdf'])->name('work.exportPdf');
    Route::get('/exportExcel',[WorkstatusController::class,'exportExcel'])->name('work.exportExcel');
    Route::post('/workstatus.import',[WorkstatusController::class,'importExcel'])->name('workstatus.import');

    Route::post('/work/progress',[WorkstatusController::class,'progressArea'])->name('work.progressArea');
    Route::get('/work/get-progress',[WorkstatusController::class,'getProgressArea']);
    Route::get('/search-tasks', [WorkstatusController::class, 'searchTasks'])->name('work.search');
    Route::post('/work/update-area-form', [WorkstatusController::class, 'updateAreaForm'])->name('work.updateAreaForm');
    Route::get('/work/get-update-area/{id}', [WorkstatusController::class, 'getUpdateArea']);
    Route::get('/work/get-task-details/{id}', [WorkstatusController::class, 'getTaskDetails']);

});
