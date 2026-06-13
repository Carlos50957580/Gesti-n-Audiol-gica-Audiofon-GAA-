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
use App\Http\Controllers\ClinicalRecordDocumentController;
use App\Http\Controllers\AudiologistFeeController;
use App\Http\Controllers\AudiologistFeeSettingController;
use App\Http\Controllers\AudiologistFeePaymentController;

Route::get('/', function () {
    return view('/auth/login');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');


// ── Usuarios (solo admin normal, no admin2) ─────────────────────────────────────
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


// ── Pacientes (admin, recepcionista, admin2) ─────────────────────────────────────
// Modal de detalle
Route::get('patients/{patient}/show-data', [PatientController::class, 'showData'])
    ->name('patients.show-data')
    ->middleware(['auth', 'role:admin,recepcionista,audiologo,admin2']);

// Modal de edición
Route::get('patients/{patient}/edit-data', [PatientController::class, 'editData'])
    ->name('patients.edit-data')
    ->middleware(['auth', 'role:admin,recepcionista,admin2']);

// AJAX store desde facturación
Route::post('api/patients', [PatientController::class, 'storeAjax'])
    ->name('api.patients.store')
    ->middleware(['auth', 'role:admin,recepcionista,admin2']);

// Resource principal
Route::resource('patients', PatientController::class)
    ->middleware(['auth', 'role:admin,recepcionista,admin2']);


// ── Sucursales (admin y admin2) ─────────────────────────────────────
Route::get('branches/{branch}/show-data', [BranchController::class, 'showData'])
    ->name('branches.show-data')
    ->middleware(['auth', 'role:admin,admin2']);

Route::get('branches/{branch}/edit-data', [BranchController::class, 'editData'])
    ->name('branches.edit-data')
    ->middleware(['auth', 'role:admin,admin2']);

Route::resource('branches', BranchController::class)
    ->middleware(['auth', 'role:admin,admin2']);


// ── Citas (admin, recepcionista, admin2) ─────────────────────────────────────
Route::get('api/patients/search', [AppointmentController::class, 'searchPatients'])
    ->name('api.patients.search')
    ->middleware(['auth']);

Route::get('appointments/{appointment}/show-data', [AppointmentController::class, 'showData'])
    ->name('appointments.show-data')
    ->middleware(['auth']);

Route::get('appointments/{appointment}/edit-data', [AppointmentController::class, 'editData'])
    ->name('appointments.edit-data')
    ->middleware(['auth']);

Route::resource('appointments', AppointmentController::class)
    ->middleware(['auth']);


// ── Servicios (solo admin normal, no admin2) ─────────────────────────────────────
Route::get('services/{service}/show-data', [ServiceController::class, 'showData'])
    ->name('services.show-data')
    ->middleware(['auth', 'role:admin']);

Route::get('services/{service}/edit-data', [ServiceController::class, 'editData'])
    ->name('services.edit-data')
    ->middleware(['auth', 'role:admin']);

Route::resource('services', ServiceController::class)
    ->middleware(['auth', 'role:admin']);


// ── Seguros Médicos (admin y admin2) ─────────────────────────────────────
Route::get('insurances/{insurance}/show-data', [InsuranceController::class, 'showData'])
    ->name('insurances.show-data')
    ->middleware(['auth', 'role:admin,admin2']);

Route::get('insurances/{insurance}/edit-data', [InsuranceController::class, 'editData'])
    ->name('insurances.edit-data')
    ->middleware(['auth', 'role:admin,admin2']);

Route::resource('insurances', InsuranceController::class)
    ->middleware(['auth', 'role:admin,admin2']);


// ── Facturación (admin, recepcionista, admin2) ─────────────────────────────────────
Route::middleware(['auth', 'role:admin,recepcionista,admin2'])->group(function () {

    // CRUD principal de facturas
    Route::resource('invoices', InvoiceController::class)
        ->only(['index', 'create', 'store', 'show']);

    // Cancelar factura (solo admin normal)
    Route::patch('invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])
        ->name('invoices.cancel')
        ->middleware('role:admin');

    // AJAX: buscar pacientes
    Route::get('api/patients/search', [InvoiceController::class, 'searchPatients'])
        ->name('api.patients.search');

    // AJAX: precio de servicio
    Route::get('api/services/{service}/price', [InvoiceController::class, 'getServicePrice'])
        ->name('api.services.price');
});


// ── Pagos (admin, recepcionista, admin2) ─────────────────────────────────────
Route::middleware(['auth', 'role:admin,recepcionista,admin2'])->group(function () {

    Route::get('receipts', [ReceiptController::class, 'index'])->name('receipts.index');
    Route::post('receipts', [ReceiptController::class, 'store'])->name('receipts.store');

    Route::get('api/receipts/invoice-data/{invoice}', [ReceiptController::class, 'invoiceData'])
        ->name('receipts.invoice-data');

    Route::get('receipts/{receipt}', [ReceiptController::class, 'show'])->name('receipts.show');
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


// ── Historia Clínica (solo audiólogo) ─────────────────────────────────────
Route::get('clinical-records/{invoice}/show-data', [ClinicalRecordController::class, 'showData'])
    ->name('clinical-records.show-data')
    ->middleware(['auth', 'role:audiologo']);
    
Route::get('clinical-records/patient/{patientId}/history', [ClinicalRecordController::class, 'patientHistory'])
    ->name('clinical-records.patient-history')
    ->middleware(['auth', 'role:audiologo']);

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


// ── Documentos de historia clínica ─────────────────────────────────────
Route::middleware(['auth', 'role:audiologo'])->group(function () {

    Route::post('clinical-records/{clinicalRecord}/documents',
        [ClinicalRecordDocumentController::class, 'store'])
        ->name('clinical-records.documents.store');

    Route::get('clinical-records/documents/{document}/download',
        [ClinicalRecordDocumentController::class, 'download'])
        ->name('clinical-records.documents.download');

    Route::delete('clinical-records/documents/{document}',
        [ClinicalRecordDocumentController::class, 'destroy'])
        ->name('clinical-records.documents.destroy');

    Route::get('clinical-records/{clinicalRecord}/documents',
        [ClinicalRecordDocumentController::class, 'index'])
        ->name('clinical-records.documents.index');
});


// ── Reportes Admin (solo admin normal) ─────────────────────────────────────
Route::get('reports/by-user', [ReportController::class, 'byUser'])
    ->name('reports.by-user')
    ->middleware(['auth', 'role:admin']);

Route::middleware(['auth', 'role:admin'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/',               [ReportController::class, 'index'])->name('index');
    Route::get('invoices',        [ReportController::class, 'invoices'])->name('invoices');
    Route::get('appointments',    [ReportController::class, 'appointments'])->name('appointments');
    Route::get('clinical-records',[ReportController::class, 'clinicalRecords'])->name('clinical-records');
    Route::get('patients',        [ReportController::class, 'patients'])->name('patients');
});


// ── Reportes Recepcionista (solo recepcionista) ─────────────────────────────────────
Route::middleware(['auth', 'role:recepcionista'])->prefix('receptionist/reports')->name('receptionist.reports.')->group(function () {
    Route::get('/',        [ReceptionistReportController::class, 'index'])->name('index');
    Route::get('summary',  [ReceptionistReportController::class, 'summary'])->name('summary');
    Route::get('invoices', [ReceptionistReportController::class, 'invoices'])->name('invoices');
    Route::get('services', [ReceptionistReportController::class, 'services'])->name('services');
});


// ── Reportes Audiólogo (solo audiólogo) ─────────────────────────────────────
Route::middleware(['auth', 'role:audiologo'])->prefix('audiologist/reports')->name('audiologist.reports.')->group(function () {
    Route::get('/',               [AudiologistReportController::class, 'index'])->name('index');
    Route::get('appointments',    [AudiologistReportController::class, 'appointments'])->name('appointments');
    Route::get('clinical-records',[AudiologistReportController::class, 'clinicalRecords'])->name('clinical-records');
});


// ── API RNC ─────────────────────────────────────
Route::get('api/rnc/{rnc}', [InvoiceController::class, 'consultRnc'])->name('api.rnc');


// ── Honorarios de audiólogos (admin y admin2) ─────────────────────────────────────
Route::prefix('audiologist-fees')->group(function () {
    Route::get('/', [AudiologistFeeController::class, 'index'])->name('audiologist-fees.index');
    Route::post('/calculate', [AudiologistFeeController::class, 'calculateFee'])->name('audiologist-fees.calculate');
    Route::post('/', [AudiologistFeeController::class, 'store'])->name('audiologist-fees.store');
    Route::put('/{id}', [AudiologistFeeController::class, 'update'])->name('audiologist-fees.update');
    Route::delete('/{id}', [AudiologistFeeController::class, 'destroy'])->name('audiologist-fees.destroy');
    Route::get('/invoice/{invoiceId}', [AudiologistFeeController::class, 'getInvoiceFees'])->name('audiologist-fees.invoice');
    
    // Configuración
    Route::get('/settings', [AudiologistFeeSettingController::class, 'index'])->name('audiologist-fees.settings');
    Route::post('/settings', [AudiologistFeeSettingController::class, 'store'])->name('audiologist-fees.settings.store');
    Route::put('/settings/{id}', [AudiologistFeeSettingController::class, 'update'])->name('audiologist-fees.settings.update');
    Route::delete('/settings/{id}', [AudiologistFeeSettingController::class, 'destroy'])->name('audiologist-fees.settings.destroy');
    Route::get('/settings/{audiologistId}/get', [AudiologistFeeSettingController::class, 'getSetting'])->name('audiologist-fees.settings.get');
    
    // Pagos
    Route::get('/payments', [AudiologistFeePaymentController::class, 'index'])->name('audiologist-fees.payments');
    Route::get('/payments/pending/{audiologistId}', [AudiologistFeePaymentController::class, 'getPendingFees'])->name('audiologist-fees.payments.pending');
    Route::post('/payments', [AudiologistFeePaymentController::class, 'store'])->name('audiologist-fees.payments.store');
    Route::get('/payments/{id}', [AudiologistFeePaymentController::class, 'show'])->name('audiologist-fees.payments.show');
    Route::delete('/payments/{id}', [AudiologistFeePaymentController::class, 'destroy'])->name('audiologist-fees.payments.destroy');
    Route::get('/{id}', [AudiologistFeeController::class, 'show'])->name('audiologist-fees.show');

})->middleware(['auth', 'role:admin,admin2']);


require __DIR__.'/auth.php';