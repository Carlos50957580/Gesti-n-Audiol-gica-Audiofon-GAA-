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
use App\Http\Controllers\Evaluador\ReporteEvaluadorController;
use App\Http\Controllers\Evaluador\RevisionEvaluadorController;
use App\Http\Controllers\MESCYT\EvaluacionesMESCYTController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\University\DocumentoPlanController;
use App\Http\Controllers\University\ReporteUniversidadController;
use Illuminate\Support\Facades\Route;

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
        Route::get('/universidades', [UniversityController::class, 'index'])->name('universidades.index');
    });


    Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Asignar evaluador a un plan
        Route::get('planes/{plan}/asignar', [AsignacionController::class, 'create'])->name('planes.asignar');
        Route::post('planes/{plan}/asignar', [AsignacionController::class, 'store'])->name('planes.asignar.store');
    });


    Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('planes', PlanEstudioAdminController::class)
            ->parameters(['planes' => 'plan']);
    });

    Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('revision', [RevisionPlanController::class, 'index'])->name('revision.index');
        Route::get('revision/{plan}', [RevisionPlanController::class, 'show'])->name('revision.show');
        Route::put('revision/{plan}/estado', [RevisionPlanController::class, 'updateEstado'])->name('revision.updateEstado');
    });


   Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/reportes', [\App\Http\Controllers\Admin\ReporteAdminController::class, 'index'])
            ->name('reportes.index');

        Route::get('/reportes/excel', [\App\Http\Controllers\Admin\ReporteAdminController::class, 'exportExcel'])
            ->name('reportes.excel');

        Route::get('/reportes/pdf', [\App\Http\Controllers\Admin\ReporteAdminController::class, 'exportPdf'])
            ->name('reportes.pdf');
    });

      Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
    // CRUD Universidades
Route::resource('universidades', \App\Http\Controllers\Admin\UniversityController::class)
    ->parameters(['universidades' => 'universidad']);

    // Rutas especiales
    Route::put('/universidades/{universidad}/aprobar', [\App\Http\Controllers\Admin\UniversityController::class, 'aprobar'])->name('universidades.aprobar');
    Route::put('/universidades/{universidad}/rechazar', [\App\Http\Controllers\Admin\UniversityController::class, 'rechazar'])->name('universidades.rechazar');
});

   


require __DIR__.'/auth.php';
