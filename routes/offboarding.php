<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Hr\OffboardingController;

    //approval settings
    Route::group(['module'=>'Hr Uploads','section'=>'documents','prefix' => 'hr', 'middleware' => ['prevent-back-history', 'auth', 'permission']], function () {
        
        Route::group(['prefix' => 'offboarding','sub_section'=>'offboarding_process'], function () {
            Route::get('/', [OffboardingController::class, 'index'])->name('offboarding-process.index');
            Route::get('/create', [OffboardingController::class, 'createProcess'])->name('offboarding-process.create');
            Route::post('/store', [OffboardingController::class, 'storeProcess'])->name('offboarding-process.store');
            Route::get('/{offboarding_process}/edit', [OffboardingController::class, 'edit'])->name('offboarding-process.edit');
            Route::put('/{offboarding_process}', [OffboardingController::class, 'updateOffBoardingProcesss'])->name('offboarding-process.update');
            Route::delete('/{offboarding_process}/delete', [OffboardingController::class, 'destroyOffboardingProcess'])->name('offboarding-process.delete');
        });

     
    });



