<?php

use App\Http\Controllers\Feedback\AdminFeedbackController;
use App\Http\Controllers\Feedback\AnonymousFeedbackController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Feedback\FeedbackCategoriesController;
use App\Http\Controllers\Feedback\EmployeeFeedbackController;
Route::group(['module'=>'Employee Feedback','section'=>'employee_feedback','prefix' => 'feedback','middleware' => ['auth', 'permission']], function(){
    Route::group(['sub_section'=>'feedback_category'],function(){
    Route::name('feedback.category.')->prefix('category')->group(  function () {
        Route::get('/',[FeedbackCategoriesController::class, 'index'])->name('index');
        Route::get('/trash',[FeedbackCategoriesController::class, 'trash'])->name('trash');
        Route::get('/create',[FeedbackCategoriesController::class, 'create'])->name('create');
        Route::get('/edit/{id}',[FeedbackCategoriesController::class, 'edit'])->name('edit');
        Route::post('/store',[FeedbackCategoriesController::class, 'store'])->name('store');
        Route::get('/{id}/view',[FeedbackCategoriesController::class, 'view'])->name('view');
        Route::put('/{id}',[FeedbackCategoriesController::class, 'update'])->name('update');
        Route::delete('/{id}/delete',[FeedbackCategoriesController::class, 'delete'])->name('delete');
        Route::delete('/{id}/restore',[FeedbackCategoriesController::class, 'restore'])->name('restore');
        Route::delete('/{id}/destroy',[FeedbackCategoriesController::class, 'destroy'])->name('destroy');
    });
});

Route::group(['sub_section'=>'employee_feedback'],function(){
    Route::name('employee.feedback.')->prefix('feedback')->group( function () {
        Route::get('/',[AdminFeedbackController::class, 'index'])->name('index');
        Route::get('/respond/{id}',[AdminFeedbackController::class, 'respond'])->name('respond');
        Route::post('/store-response',[AdminFeedbackController::class, 'storeResponse'])->name('store-reponse');
        Route::get('/{id}/view',[AdminFeedbackController::class, 'view'])->name('view');
        Route::put('/{id}',[AdminFeedbackController::class, 'update'])->name('update');
        Route::delete('/{id}/delete',[AdminFeedbackController::class, 'destroy'])->name('delete');
    });
});
//routes moved to ess file. 
//     Route::group(['sub_section'=>'ess_feedback'],function(){
//     Route::name('ess.feedback.')->prefix('ess')->group( function () {
//         Route::get('/',[EmployeeFeedbackController::class, 'index'])->name('index');
//         Route::get('/create',[EmployeeFeedbackController::class, 'create'])->name('create');
//         Route::post('/store',[EmployeeFeedbackController::class, 'store'])->name('store');
//         Route::get('/{id}/view',[EmployeeFeedbackController::class, 'view'])->name('view');
//         Route::get('/show/{id}',[EmployeeFeedbackController::class, 'show'])->name('show');
//         Route::put('/{id}/update',[EmployeeFeedbackController::class, 'update'])->name('update');
//         Route::delete('/{id}/delete',[EmployeeFeedbackController::class, 'destroy'])->name('delete');
//         Route::get('/create-anonymous',[AnonymousFeedbackController::class, 'createAnonymous'])->name('anonymous.create');
//         Route::post('/store-anonymous',[AnonymousFeedbackController::class, 'storeAnonymous'])->name('anonymous.store');
//     });
// });
    Route::group(['sub_section'=>'annonymous_feedback'],function(){
    Route::name('anonymous.feedback.')->prefix('anonymous')->group( function () {
        Route::get('/',[AnonymousFeedbackController::class, 'index'])->name('index');
        Route::get('/create',[AnonymousFeedbackController::class, 'create'])->name('create');
        Route::post('/store',[AnonymousFeedbackController::class, 'store'])->name('store');
        Route::get('/{id}/view',[AnonymousFeedbackController::class, 'view'])->name('view');
        Route::put('/{id}',[AnonymousFeedbackController::class, 'update'])->name('update');
        Route::delete('/{id}/delete',[AnonymousFeedbackController::class, 'destroy'])->name('delete');
        Route::get('/review/{id}',[AnonymousFeedbackController::class, 'review'])->name('review');
        Route::post('/store-review',[AnonymousFeedbackController::class, 'storeReview'])->name('store-review');
    });
});
});