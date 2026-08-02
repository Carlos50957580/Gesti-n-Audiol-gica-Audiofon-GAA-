<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ClinicalRecordController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReceptionistReportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DoctorReportController;
use App\Http\Controllers\DoctorAppointmentController;
use App\Http\Controllers\DoctorClinicalRecordController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\InvoiceController;

// ============================================
// RUTAS PÚBLICAS
// ============================================
Route::get('/', function () {
    return view('auth.login');
});

// ============================================
// RUTAS AUTENTICADAS (con middleware active)
// ============================================
Route::middleware(['auth', 'active'])->group(function () {

    // ── Dashboard ─────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Perfil ─────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── USUARIOS (solo admin) ─────────────────────────────────────
    Route::resource('usuarios', UserController::class)
        ->middleware(['role:admin'])
        ->names([
            'index'   => 'admin.usuarios.index',
            'create'  => 'admin.usuarios.create',
            'store'   => 'admin.usuarios.store',
            'edit'    => 'admin.usuarios.edit',
            'update'  => 'admin.usuarios.update',
            'destroy' => 'admin.usuarios.destroy',
        ]);

    Route::middleware(['role:admin'])->group(function () {
        Route::post('usuarios/{usuario}/activate', [UserController::class, 'activate'])->name('admin.usuarios.activate');
        Route::post('usuarios/{usuario}/deactivate', [UserController::class, 'deactivate'])->name('admin.usuarios.deactivate');
        Route::post('usuarios/{id}/restore', [UserController::class, 'restore'])->name('admin.usuarios.restore');
        Route::delete('usuarios/{id}/force-delete', [UserController::class, 'forceDelete'])->name('admin.usuarios.force-delete');
        Route::get('usuarios/{usuario}/edit-data', [UserController::class, 'editData'])->name('admin.usuarios.edit-data');
    });

    // ── PACIENTES (admin y recepcionista) ─────────────────────────────────────
    Route::middleware(['role:admin,recepcionista'])->group(function () {
        Route::get('patients/{patient}/show-data', [PatientController::class, 'showData'])->name('patients.show-data');
        Route::get('patients/{patient}/edit-data', [PatientController::class, 'editData'])->name('patients.edit-data');
        Route::post('api/patients', [PatientController::class, 'storeAjax'])->name('api.patients.store');
        Route::resource('patients', PatientController::class);
    });

    // ── SUCURSALES (solo admin) ─────────────────────────────────────
    Route::middleware(['role:admin'])->group(function () {
        Route::get('branches/{branch}/show-data', [BranchController::class, 'showData'])->name('branches.show-data');
        Route::get('branches/{branch}/edit-data', [BranchController::class, 'editData'])->name('branches.edit-data');
        Route::resource('branches', BranchController::class);
    });



    // ── SERVICIOS (solo admin) ─────────────────────────────────────
    Route::middleware(['role:admin'])->group(function () {
        Route::get('services/{service}/show-data', [ServiceController::class, 'showData'])->name('services.show-data');
        Route::get('services/{service}/edit-data', [ServiceController::class, 'editData'])->name('services.edit-data');
        Route::resource('services', ServiceController::class);
    });

    // ── CATEGORÍAS DE SERVICIOS (solo admin) ─────────────────────────────────────
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/service-categories', [ServiceCategoryController::class, 'index'])->name('service-categories.index');
        Route::get('/service-categories/create', [ServiceCategoryController::class, 'create'])->name('service-categories.create');
        Route::post('/service-categories', [ServiceCategoryController::class, 'store'])->name('service-categories.store');
        Route::get('/service-categories/{serviceCategory}/edit', [ServiceCategoryController::class, 'edit'])->name('service-categories.edit');
        Route::put('/service-categories/{serviceCategory}', [ServiceCategoryController::class, 'update'])->name('service-categories.update');
        Route::delete('/service-categories/{serviceCategory}', [ServiceCategoryController::class, 'destroy'])->name('service-categories.destroy');
    });

    // ── IMPUESTOS (solo admin) ─────────────────────────────────────
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('taxes', TaxController::class);
    });

    // ── SEGUROS MÉDICOS (solo admin) ─────────────────────────────────────
    Route::middleware(['role:admin'])->group(function () {
        Route::get('insurances/{insurance}/show-data', [InsuranceController::class, 'showData'])->name('insurances.show-data');
        Route::get('insurances/{insurance}/edit-data', [InsuranceController::class, 'editData'])->name('insurances.edit-data');
        Route::resource('insurances', InsuranceController::class);
    });

    // ── FACTURACIÓN (admin y recepcionista) ─────────────────────────────────────
    Route::middleware(['role:admin,recepcionista'])->group(function () {
        Route::resource('invoices', InvoiceController::class);
        Route::post('/invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');
        Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    });

    // ── PAGOS / RECIBOS (admin y recepcionista) ─────────────────────────────────────
    Route::middleware(['role:admin,recepcionista'])->group(function () {
        Route::get('receipts', [ReceiptController::class, 'index'])->name('receipts.index');
        Route::post('receipts', [ReceiptController::class, 'store'])->name('receipts.store');
        Route::get('api/receipts/invoice-data/{invoice}', [ReceiptController::class, 'invoiceData'])->name('receipts.invoice-data');
        Route::get('receipts/{receipt}', [ReceiptController::class, 'show'])->name('receipts.show');
    });

    // ── HISTORIA CLÍNICA (solo médicos) ─────────────────────────────────────
    Route::middleware(['role:medico'])->group(function () {
        Route::get('clinical-records', [DoctorClinicalRecordController::class, 'index'])->name('clinical-records.index');
        Route::get('clinical-records/{invoice}/edit', [DoctorClinicalRecordController::class, 'edit'])->name('clinical-records.edit');
        Route::put('clinical-records/{invoice}', [DoctorClinicalRecordController::class, 'update'])->name('clinical-records.update');
        Route::get('clinical-records/{invoice}/show', [DoctorClinicalRecordController::class, 'show'])->name('clinical-records.show');
        Route::get('clinical-records/{invoice}/show-data', [DoctorClinicalRecordController::class, 'showData'])->name('clinical-records.show-data');
        Route::get('clinical-records/patient/{patientId}/history', [DoctorClinicalRecordController::class, 'patientHistory'])->name('clinical-records.patient-history');
    });

    // ── REPORTES ADMIN (solo admin) ─────────────────────────────────────
    Route::middleware(['role:admin'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('invoices', [ReportController::class, 'invoices'])->name('invoices');
        Route::get('appointments', [ReportController::class, 'appointments'])->name('appointments');
        Route::get('clinical-records', [ReportController::class, 'clinicalRecords'])->name('clinical-records');
        Route::get('patients', [ReportController::class, 'patients'])->name('patients');
        Route::get('by-user', [ReportController::class, 'byUser'])->name('by-user');
    });

    // ── REPORTES RECEPCIONISTA (solo recepcionista) ─────────────────────────────────────
    Route::middleware(['role:recepcionista'])->prefix('receptionist/reports')->name('receptionist.reports.')->group(function () {
        Route::get('/', [ReceptionistReportController::class, 'index'])->name('index');
        Route::get('summary', [ReceptionistReportController::class, 'summary'])->name('summary');
        Route::get('invoices', [ReceptionistReportController::class, 'invoices'])->name('invoices');
        Route::get('services', [ReceptionistReportController::class, 'services'])->name('services');
    });

    // ── REPORTES MÉDICO (solo médicos) ─────────────────────────────────────
    Route::middleware(['role:medico'])->prefix('doctor/reports')->name('doctor.reports.')->group(function () {
        Route::get('/', [DoctorReportController::class, 'index'])->name('index');
        Route::get('appointments', [DoctorReportController::class, 'appointments'])->name('appointments');
        Route::get('clinical-records', [DoctorReportController::class, 'clinicalRecords'])->name('clinical-records');
    });

}); // Fin del grupo auth + active

// ============================================
// RUTAS API (sin active, solo autenticación)
// ============================================
Route::middleware(['auth'])->prefix('api')->name('api.')->group(function () {

    // ── Pacientes ─────────────────────────────────────
    Route::get('/patients/search', [InvoiceController::class, 'searchPatients'])->name('patients.search');

    // ── Servicios ─────────────────────────────────────
    Route::get('/services/by-category/{categoryId}', [ServiceController::class, 'getByCategory'])->name('services.by-category');
    Route::get('/services/detail/{id}', [ServiceController::class, 'getDetail'])->name('services.detail');
    Route::get('/services/coverage/{serviceId}/{insuranceId}', [ServiceController::class, 'getCoverage'])->name('services.coverage');

    // ── Impuestos ─────────────────────────────────────
    Route::get('/taxes/active', [TaxController::class, 'getActiveTaxes'])->name('taxes.active');

    // ── Doctores ─────────────────────────────────────
    Route::get('/doctors', [UserController::class, 'getDoctors'])->name('doctors');

    // ── Sucursales ─────────────────────────────────────
    Route::get('/branches', [InvoiceController::class, 'getBranches'])->name('branches');

    // ── RNC ─────────────────────────────────────
    Route::get('/rnc/{rnc}', [InvoiceController::class, 'consultRnc'])->name('rnc');
});

// ── CITAS ─────────────────────────────────────
// Admin y recepcionista pueden gestionar citas
Route::middleware(['auth', 'role:admin,recepcionista'])->group(function () {
    Route::get('api/patients/search', [AppointmentController::class, 'searchPatients'])->name('api.patients.search');
    Route::get('appointments/{appointment}/show-data', [AppointmentController::class, 'showData'])->name('appointments.show-data');
    Route::get('appointments/{appointment}/edit-data', [AppointmentController::class, 'editData'])->name('appointments.edit-data');
    Route::resource('appointments', AppointmentController::class);
});

// ── MIS CITAS (solo médicos) ──────────────────
Route::middleware(['auth'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('appointments', [DoctorAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('appointments/{appointment}', [DoctorAppointmentController::class, 'show'])->name('appointments.show');
    Route::patch('appointments/{appointment}/status', [DoctorAppointmentController::class, 'updateStatus'])->name('appointments.status');
    Route::patch('appointments/{appointment}/notes', [DoctorAppointmentController::class, 'updateNotes'])->name('appointments.notes');
});

require __DIR__.'/auth.php';