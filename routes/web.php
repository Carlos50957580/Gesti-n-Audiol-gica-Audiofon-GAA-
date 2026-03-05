<?php

use App\Http\Controllers\Admin\AsignacionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PlanEstudioAdminController;
use App\Http\Controllers\Admin\ReporteAdminController;
use App\Http\Controllers\Admin\RevisionPlanController;
//use App\Http\Controllers\Admin\PlanEstudioController;
use App\Http\Controllers\Admin\UniversityController;
use App\Http\Controllers\University\PlanEstudioController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Evaluador\ReporteEvaluadorController;
use App\Http\Controllers\Evaluador\RevisionEvaluadorController;
use App\Http\Controllers\MESCYT\EvaluacionesMESCYTController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\University\DocumentoPlanController;
use App\Http\Controllers\University\ReporteUniversidadController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;

Route::get('/', function () {
    return view('/auth/login');
});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');


     /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
   */

    Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
            ->name('dashboard');
    });
    
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
Route::resource('usuarios', \App\Http\Controllers\Admin\UserController::class);
        
    });


    

Route::middleware(['auth'])->group(function () {

    Route::middleware('role:admin,recepcionista,audiologo')
        ->resource('patients', PatientController::class);

});

Route::middleware(['auth','role:admin,recepcionista,audiologo'])
    ->resource('appointments', AppointmentController::class);

require __DIR__.'/auth.php';
