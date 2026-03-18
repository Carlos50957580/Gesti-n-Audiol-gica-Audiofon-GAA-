<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AudiologistAppointmentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ClinicalRecordController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReceptionistReportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AudiologistReportController;


Route::get('/', function () {
    return view('/auth/login');
});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');


    
    Route::resource('usuarios', UserController::class)
    ->middleware(['auth', 'role:admin'])
    ->names([
        'index'   => 'admin.usuarios.index',
        'create'  => 'admin.usuarios.create',
        'store'   => 'admin.usuarios.store',
        'edit'    => 'admin.usuarios.edit',
        'update'  => 'admin.usuarios.update',
        'destroy' => 'admin.usuarios.destroy',
    ]);
    
Route::get('usuarios/{usuario}/edit-data', [UserController::class, 'editData'])
    ->name('admin.usuarios.edit-data')
    ->middleware(['auth', 'role:admin']);


    

// Modal de detalle
Route::get('patients/{patient}/show-data', [PatientController::class, 'showData'])
    ->name('patients.show-data')
    ->middleware(['auth', 'role:admin,recepcionista,audiologo']);

// Modal de edición
Route::get('patients/{patient}/edit-data', [PatientController::class, 'editData'])
    ->name('patients.edit-data')
    ->middleware(['auth', 'role:admin,recepcionista']);

// AJAX store desde facturación
Route::post('api/patients', [PatientController::class, 'storeAjax'])
    ->name('api.patients.store')
    ->middleware(['auth', 'role:admin,recepcionista']);

// ── Resource existente ──────────────────────────────────────────
Route::resource('patients', PatientController::class)
    ->middleware(['auth', 'role:admin,recepcionista']);



Route::get('branches/{branch}/show-data', [BranchController::class, 'showData'])
    ->name('branches.show-data')
    ->middleware(['auth', 'role:admin']);

Route::get('branches/{branch}/edit-data', [BranchController::class, 'editData'])
    ->name('branches.edit-data')
    ->middleware(['auth', 'role:admin']);

// Tu resource existente (ya lo tienes):
Route::resource('branches', BranchController::class)
    ->middleware(['auth', 'role:admin']);




Route::get('api/patients/search', [AppointmentController::class, 'searchPatients'])
    ->name('api.patients.search')
    ->middleware(['auth']);
 
// Datos para modales AJAX
Route::get('appointments/{appointment}/show-data', [AppointmentController::class, 'showData'])
    ->name('appointments.show-data')
    ->middleware(['auth']);
 
Route::get('appointments/{appointment}/edit-data', [AppointmentController::class, 'editData'])
    ->name('appointments.edit-data')
    ->middleware(['auth']);
 
// Resource principal
Route::resource('appointments', AppointmentController::class)
    ->middleware(['auth']);
 

    Route::get('services/{service}/show-data', [ServiceController::class, 'showData'])
    ->name('services.show-data')
    ->middleware(['auth', 'role:admin']);

Route::get('services/{service}/edit-data', [ServiceController::class, 'editData'])
    ->name('services.edit-data')
    ->middleware(['auth', 'role:admin']);

// Tu resource existente (ya lo tienes):
Route::resource('services', ServiceController::class)
    ->middleware(['auth', 'role:admin']);


    Route::get('insurances/{insurance}/show-data', [InsuranceController::class, 'showData'])
    ->name('insurances.show-data')
    ->middleware(['auth', 'role:admin']);

Route::get('insurances/{insurance}/edit-data', [InsuranceController::class, 'editData'])
    ->name('insurances.edit-data')
    ->middleware(['auth', 'role:admin']);

// Tu resource existente (ya lo tienes):
Route::resource('insurances', InsuranceController::class)
    ->middleware(['auth', 'role:admin']);


Route::middleware(['auth', 'role:admin,recepcionista'])->group(function () {

    // CRUD principal de facturas
    Route::resource('invoices', InvoiceController::class)
        ->only(['index', 'create', 'store', 'show']);

    // Cancelar factura (solo admin)
    Route::patch('invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])
        ->name('invoices.cancel')
        ->middleware('role:admin');

    // AJAX: buscar pacientes
    Route::get('api/patients/search', [InvoiceController::class, 'searchPatients'])
        ->name('api.patients.search');

    // AJAX: precio de servicio
    Route::get('api/services/{service}/price', [InvoiceController::class, 'getServicePrice'])
        ->name('api.services.price');

    // AJAX: crear paciente desde modal
    Route::post('api/patients', [PatientController::class, 'storeAjax'])
        ->name('api.patients.store');
});


Route::middleware(['auth', 'role:admin,recepcionista'])->group(function () {
 
    // Index: facturas pendientes
    Route::get('receipts', [ReceiptController::class, 'index'])->name('receipts.index');
 
    // Store: registrar pago
    Route::post('receipts', [ReceiptController::class, 'store'])->name('receipts.store');
 
    // Show: recibo imprimible
    Route::get('receipts/{receipt}', [ReceiptController::class, 'show'])->name('receipts.show');
 
    // AJAX: datos de factura para el modal
    Route::get('api/receipts/invoice-data/{invoice}', [ReceiptController::class, 'invoiceData'])
        ->name('receipts.invoice-data');
 
});

// ── Citas del audiólogo (solo su propio listado) ──────────────────────────────
Route::middleware(['auth', 'role:audiologo'])->prefix('audiologist')->name('audiologist.')->group(function () {
 
    Route::get('appointments',              [AudiologistAppointmentController::class, 'index'])
        ->name('appointments.index');
 
    Route::patch('appointments/{appointment}/status', [AudiologistAppointmentController::class, 'updateStatus'])
        ->name('appointments.status');
 
    Route::patch('appointments/{appointment}/notes',  [AudiologistAppointmentController::class, 'updateNotes'])
        ->name('appointments.notes');
 
    Route::get('appointments/{appointment}/show-data', [AudiologistAppointmentController::class, 'showData'])
        ->name('appointments.show-data');
});




// routes/web.php

// Antes del resource / rutas manuales
Route::get('clinical-records/{invoice}/show-data', [ClinicalRecordController::class, 'showData'])
    ->name('clinical-records.show-data')
    ->middleware(['auth', 'role:audiologo']);
    
// Historial AJAX — antes del resource
Route::get('clinical-records/patient/{patientId}/history', [ClinicalRecordController::class, 'patientHistory'])
    ->name('clinical-records.patient-history')
    ->middleware(['auth', 'role:audiologo']);

// Rutas manuales (no resource, porque el parámetro es invoice)
Route::get('clinical-records', [ClinicalRecordController::class, 'index'])
    ->name('clinical-records.index')
    ->middleware(['auth', 'role:audiologo']);

Route::get('clinical-records/{invoice}/edit', [ClinicalRecordController::class, 'edit'])
    ->name('clinical-records.edit')
    ->middleware(['auth', 'role:audiologo']);

Route::put('clinical-records/{invoice}', [ClinicalRecordController::class, 'update'])
    ->name('clinical-records.update')
    ->middleware(['auth', 'role:audiologo']);

Route::get('clinical-records/{invoice}/show', [ClinicalRecordController::class, 'show'])
    ->name('clinical-records.show')
    ->middleware(['auth', 'role:audiologo']);


    // routes/web.php
Route::middleware(['auth', 'role:admin'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/',               [ReportController::class, 'index'])->name('index');
    Route::get('invoices',        [ReportController::class, 'invoices'])->name('invoices');
    Route::get('appointments',    [ReportController::class, 'appointments'])->name('appointments');
    Route::get('clinical-records',[ReportController::class, 'clinicalRecords'])->name('clinical-records');
    Route::get('patients',        [ReportController::class, 'patients'])->name('patients');
});


Route::middleware(['auth', 'role:recepcionista'])->prefix('receptionist/reports')->name('receptionist.reports.')->group(function () {
    Route::get('/',        [ReceptionistReportController::class, 'index'])->name('index');
    Route::get('summary',  [ReceptionistReportController::class, 'summary'])->name('summary');
    Route::get('invoices', [ReceptionistReportController::class, 'invoices'])->name('invoices');
    Route::get('services', [ReceptionistReportController::class, 'services'])->name('services');
});

Route::middleware(['auth', 'role:audiologo'])->prefix('audiologist/reports')->name('audiologist.reports.')->group(function () {
    Route::get('/',               [AudiologistReportController::class, 'index'])->name('index');
    Route::get('appointments',    [AudiologistReportController::class, 'appointments'])->name('appointments');
    Route::get('clinical-records',[AudiologistReportController::class, 'clinicalRecords'])->name('clinical-records');
});
require __DIR__.'/auth.php';
