<?php

use App\Http\Controllers\Disciplinary\DisciplinaryCaseController;
use App\Http\Controllers\Disciplinary\DisciplinaryCategoryController;
use App\Http\Controllers\Disciplinary\DiscplinaryCaseActionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Feedback\EmployeeFeedbackController;
Route::group(['module'=>'Disciplinary','section'=>'disciplinary','prefix' => 'disciplinary','middleware' => ['auth', 'permission']], function(){

    Route::group( ['sub_section'=>'disciplinary_category','prefix'=>'category','as'=>'disciplinary.category.'], function () {
        Route::get('/',[DisciplinaryCategoryController::class, 'index'])->name('index');
        Route::get('/trash',[DisciplinaryCategoryController::class, 'trash'])->name('trash');
        Route::get('/create',[DisciplinaryCategoryController::class, 'create'])->name('create');
        Route::get('/edit/{id}',[DisciplinaryCategoryController::class, 'edit'])->name('edit');
        Route::post('/store',[DisciplinaryCategoryController::class, 'store'])->name('store');
        Route::get('/{id}/view',[DisciplinaryCategoryController::class, 'view'])->name('view');
        Route::put('/update/{id}',[DisciplinaryCategoryController::class, 'update'])->name('update');
        Route::delete('/{id}/delete',[DisciplinaryCategoryController::class, 'delete'])->name('delete');
        Route::post('/{id}/restore',[DisciplinaryCategoryController::class, 'restore'])->name('restore');
        Route::delete('/{id}/destroy',[DisciplinaryCategoryController::class, 'destroy'])->name('destroy');
    });
    Route::group( ['sub_section'=>'disciplinary_cases','prefix'=>'cases','as'=>'disciplinary.cases.'],function () {
        Route::get('/',[DisciplinaryCaseController::class, 'index'])->name('index');
        Route::get('/create',[DisciplinaryCaseController::class, 'create'])->name('create');
        Route::get('/edit/{id}',[DisciplinaryCaseController::class, 'edit'])->name('edit');
        Route::post('/store',[DisciplinaryCaseController::class, 'store'])->name('store');
        Route::get('/{id}/view',[DisciplinaryCaseController::class, 'view'])->name('view');
        Route::put('/{id}',[DisciplinaryCaseController::class, 'update'])->name('update');
        Route::delete('/{id}/delete',[DisciplinaryCaseController::class, 'delete'])->name('delete');
        Route::delete('/{id}/destroy',[DisciplinaryCaseController::class, 'destroy'])->name('destroy');
        Route::get('/closed',[DisciplinaryCaseController::class, 'closed'])->name('closed');
        Route::put('/action/{id}',[DisciplinaryCaseController::class, 'action'])->name('action');
        Route::put('/close/{id}',[DisciplinaryCaseController::class, 'close'])->name('close');
        Route::put('/reopen/{id}',[DisciplinaryCaseController::class, 'reOpen'])->name('reopen');
        Route::get('/trash',[DisciplinaryCaseController::class, 'trash'])->name('trash');
        Route::post('/{id}/restore',[DisciplinaryCaseController::class, 'restore'])->name('restore');
    });
    Route::group( ['sub_section'=>'disciplinary_case_actions','prefix'=>'cases-action','as'=>'disciplinary.cases.action.'],function () {
        Route::get('/',[DiscplinaryCaseActionController::class, 'index'])->name('index');
        Route::get('/{id}/view',[DiscplinaryCaseActionController::class, 'view'])->name('view');
        Route::put('/{id}',[DiscplinaryCaseActionController::class, 'update'])->name('update');
        Route::delete('/{id}/delete',[DiscplinaryCaseActionController::class, 'delete'])->name('delete');
        Route::delete('/{id}/destroy',[DiscplinaryCaseActionController::class, 'destroy'])->name('destroy');
        Route::get('/closed',[DiscplinaryCaseActionController::class, 'closed'])->name('closed');
        Route::put('/action/{id}',[DiscplinaryCaseActionController::class, 'action'])->name('action');
    }) ;
   
});