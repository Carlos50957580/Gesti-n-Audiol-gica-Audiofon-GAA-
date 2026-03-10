<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\InvoiceController;

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

Route::middleware(['auth','role:admin'])->group(function () {

    Route::resource('branches', BranchController::class);

});

Route::middleware(['auth','role:admin,recepcionista,audiologo'])
    ->resource('appointments', AppointmentController::class);

    Route::resource('services', ServiceController::class)
    ->middleware(['auth','role:admin']);


    Route::resource('insurances', InsuranceController::class)
    ->middleware(['auth','role:admin']);


require __DIR__.'/auth.php';
