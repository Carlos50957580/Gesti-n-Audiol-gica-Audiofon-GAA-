<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Receiptcontroller;
use App\Http\Controllers\ReceptionistReportController;
use App\Http\Controllers\DoctorAppointmentController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DoctorFeeController;
use App\Http\Controllers\DoctorFeePaymentController;
use App\Http\Controllers\DoctorFeeSettingController;
use App\Http\Controllers\Settings\CompanyController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\ProductInvoiceController;
use App\Http\Controllers\ProductReceiptController;
use App\Http\Controllers\ClinicalRecordController;
use App\Http\Controllers\ProductReportController;
use App\Http\Controllers\AdminReportController;


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


 // ── INVENTARIO (solo admin) ──────────────────────────────────
    Route::middleware(['role:admin'])->group(function () {
        
        // Categorías
        Route::resource('product-categories', ProductCategoryController::class)
            ->names('product-categories');

        // Proveedores
        Route::resource('suppliers', SupplierController::class);

        // Productos
        Route::resource('products', ProductController::class);

        // Movimientos
        Route::prefix('stock-movements')->name('stock-movements.')->group(function () {
            Route::get('/', [StockMovementController::class, 'index'])->name('index');
            
            Route::get('/entry/create', [StockMovementController::class, 'createEntry'])->name('entry.create');
            Route::post('/entry', [StockMovementController::class, 'storeEntry'])->name('entry.store');
            
            Route::get('/exit/create', [StockMovementController::class, 'createExit'])->name('exit.create');
            Route::post('/exit', [StockMovementController::class, 'storeExit'])->name('exit.store');
            
            Route::get('/transfer/create', [StockMovementController::class, 'createTransfer'])->name('transfer.create');
            Route::post('/transfer', [StockMovementController::class, 'storeTransfer'])->name('transfer.store');
            
            Route::get('/{stockMovement}', [StockMovementController::class, 'show'])->name('show');
            Route::post('/{stockMovement}/confirm', [StockMovementController::class, 'confirm'])->name('confirm');
            Route::post('/{stockMovement}/cancel', [StockMovementController::class, 'cancel'])->name('cancel');
        });

        // API
        Route::get('/api/product-stock', [StockMovementController::class, 'getProductStock'])
            ->name('api.product-stock');
    });

// ============================================
// FACTURACIÓN DE PRODUCTOS (admin + recepcionista)
// ============================================

    // ── API: Búsqueda de pacientes ─────────────────────
    Route::get('/api/product-invoices/patients/search', [ProductInvoiceController::class, 'searchPatients']);

    // ── API: Productos por categoría ───────────────────
    Route::get('/api/product-invoices/products/by-category', [ProductInvoiceController::class, 'getProductsByCategory']);

    // ── FACTURAS DE PRODUCTOS ─────────────────────────
    Route::get('/product-invoices', [ProductInvoiceController::class, 'index']);
    Route::get('/product-invoices/create', [ProductInvoiceController::class, 'create']);
    Route::post('/product-invoices', [ProductInvoiceController::class, 'store']);
 Route::get('/product-invoices/{productInvoice}', [ProductInvoiceController::class, 'show'])
    ->name('product-invoices.show');
    Route::post('/product-invoices/{productInvoice}/cancel', [ProductInvoiceController::class, 'cancel']);
    Route::get('/product-invoices/{productInvoice}/print', [ProductInvoiceController::class, 'print']);
    Route::post('/product-invoices/{productInvoice}/enviar-ef2', 
    [ProductInvoiceController::class, 'enviarEf2'])
    ->name('product-invoices.enviar-ef2');

    // ── PAGOS DE PRODUCTOS ─────────────────────────
    Route::get('/product-receipts', [ProductReceiptController::class, 'index']);
    Route::get('/product-receipts/create/{productInvoice}', [ProductReceiptController::class, 'create']);
    Route::post('/product-receipts/{productInvoice}', [ProductReceiptController::class, 'store']);
    Route::get('/product-receipts/{productReceipt}', [ProductReceiptController::class, 'show'])
    ->name('product-receipts.show');
    Route::get('/product-receipts/{productReceipt}/print', [ProductReceiptController::class, 'print']);
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
        Route::get('receipts', [Receiptcontroller::class, 'index'])->name('receipts.index');
        Route::post('receipts', [Receiptcontroller::class, 'store'])->name('receipts.store');
        Route::get('api/receipts/invoice-data/{invoice}', [Receiptcontroller::class, 'invoiceData'])->name('receipts.invoice-data');
        Route::get('receipts/{receipt}', [Receiptcontroller::class, 'show'])->name('receipts.show');
    });


// ── REPORTES RECEPCIONISTA ─────────────────────────────────────
Route::middleware(['role:recepcionista'])->prefix('receptionist/reports')->name('receptionist.reports.')->group(function () {
    Route::get('/', [ReceptionistReportController::class, 'index'])->name('index');
    Route::get('summary', [ReceptionistReportController::class, 'summary'])->name('summary');
    Route::get('invoices', [ReceptionistReportController::class, 'invoices'])->name('invoices');
    Route::get('services', [ReceptionistReportController::class, 'services'])->name('services');
    Route::get('print', [ReceptionistReportController::class, 'print'])->name('print');
});


    // ── HONORARIOS MÉDICOS ─────────────────────────────────────
    Route::prefix('doctor-fees')->name('doctor-fees.')->middleware(['auth', 'role:admin'])->group(function () {

        // ════════════════════════════════════════════════════════════
        // RUTAS FIJAS (DEBEN IR ANTES DE LAS RUTAS CON PARÁMETROS)
        // ════════════════════════════════════════════════════════════

        // ── Configuración ──────────────────────────────────────────
        Route::get('/settings', [DoctorFeeSettingController::class, 'index'])->name('settings');
        Route::post('/settings', [DoctorFeeSettingController::class, 'store'])->name('settings.store');
        Route::get('/settings/{id}', [DoctorFeeSettingController::class, 'show'])->name('settings.show');
        Route::put('/settings/{id}', [DoctorFeeSettingController::class, 'update'])->name('settings.update');
        Route::delete('/settings/{id}', [DoctorFeeSettingController::class, 'destroy'])->name('settings.destroy');
        Route::get('/settings/{doctorId}/get', [DoctorFeeSettingController::class, 'getSetting'])->name('settings.get');

        // ── Pagos ──────────────────────────────────────────────────
        Route::get('/payments', [DoctorFeePaymentController::class, 'index'])->name('payments');
        Route::get('/payments/pending/{doctorId}', [DoctorFeePaymentController::class, 'getPendingFees'])->name('payments.pending');
        Route::post('/payments', [DoctorFeePaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/{id}', [DoctorFeePaymentController::class, 'show'])->name('payments.show');
        Route::delete('/payments/{id}', [DoctorFeePaymentController::class, 'destroy'])->name('payments.destroy');

        // ── Honorarios ─────────────────────────────────────────────
        Route::get('/', [DoctorFeeController::class, 'index'])->name('index');
        Route::post('/calculate', [DoctorFeeController::class, 'calculateFee'])->name('calculate');
        Route::post('/', [DoctorFeeController::class, 'store'])->name('store');
        Route::get('/invoice/{invoiceId}', [DoctorFeeController::class, 'getInvoiceFees'])->name('invoice');

        // ════════════════════════════════════════════════════════════
        // RUTAS CON PARÁMETROS (DEBEN IR AL FINAL)
        // ════════════════════════════════════════════════════════════
        Route::get('/{id}', [DoctorFeeController::class, 'show'])->name('show');
        Route::put('/{id}', [DoctorFeeController::class, 'update'])->name('update');
        Route::delete('/{id}', [DoctorFeeController::class, 'destroy'])->name('destroy');
    });

    // ════════════════════════════════════════════════════════════════
    // 🆕 CONFIGURACIÓN DE EMPRESA (solo admin)
    // ════════════════════════════════════════════════════════════════
    Route::middleware(['role:admin'])->prefix('settings')->name('settings.')->group(function () {
        Route::get('/company', [CompanyController::class, 'index'])->name('company');
        Route::put('/company', [CompanyController::class, 'update'])->name('company.update');
    });

}); // Fin del grupo auth + active

// ============================================
// RUTAS API (sin active, solo autenticación)
// ============================================
Route::middleware(['auth'])->prefix('api')->name('api.')->group(function () {

    // ── Pacientes (facturación) ─────────────────────────────────────
    Route::get('/invoices/patients/search', [InvoiceController::class, 'searchPatients'])
        ->name('invoices.patients.search');

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
    Route::get('api/patients/search', [AppointmentController::class, 'searchPatients'])
        ->name('api.patients.search');
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


// ── REPORTES ADMIN ─────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin/reports')->name('admin.reports.')->group(function () {
    Route::get('/', [AdminReportController::class, 'index'])->name('index');
    Route::get('/invoices', [AdminReportController::class, 'invoices'])->name('invoices');
    Route::get('/appointments', [AdminReportController::class, 'appointments'])->name('appointments');
    Route::get('/patients', [AdminReportController::class, 'patients'])->name('patients');
    Route::get('/services', [AdminReportController::class, 'services'])->name('services');
    Route::get('/fees', [AdminReportController::class, 'fees'])->name('fees');
    Route::get('/export', [AdminReportController::class, 'export'])->name('export');
});



// ── Reportes de Ventas de Productos ──────────────
Route::middleware(['auth', 'active', 'role:admin'])->group(function () {
    Route::get('/product-reports', [ProductReportController::class, 'index']);
});

Route::middleware(['auth', 'active', 'role:admin,recepcionista'])->group(function () {
    Route::get('/product-reports/cashier', [ProductReportController::class, 'cashier']);
    Route::get('/product-reports/cashier/print', [ProductReportController::class, 'cashierPrint']);
});

use App\Http\Controllers\NcfSequenceController;

// ── Gestión de NCF (solo admin) ─────────────────
Route::middleware(['auth', 'active', 'role:admin'])->group(function () {
    Route::get('/ncf-sequences', [NcfSequenceController::class, 'index']);
    Route::get('/ncf-sequences/create', [NcfSequenceController::class, 'create']);
    Route::post('/ncf-sequences', [NcfSequenceController::class, 'store']);
    Route::get('/ncf-sequences/{ncfSequence}', [NcfSequenceController::class, 'show']);
    Route::get('/ncf-sequences/{ncfSequence}/edit', [NcfSequenceController::class, 'edit']);
    Route::put('/ncf-sequences/{ncfSequence}', [NcfSequenceController::class, 'update']);
    Route::delete('/ncf-sequences/{ncfSequence}', [NcfSequenceController::class, 'destroy']);
    Route::post('/ncf-sequences/{ncfSequence}/toggle', [NcfSequenceController::class, 'toggle']);

    // API
    Route::get('/api/ncf-sequences/next-available', [NcfSequenceController::class, 'getNextAvailable']);
});


use App\Http\Controllers\NcfLookupController;

// ── API: Consulta de NCF ─────────────────────────
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/api/ncf/next-available', [NcfLookupController::class, 'nextAvailable']);
});



// ============================================
// HISTORIAS CLÍNICAS
// ============================================
Route::middleware(['auth', 'active'])->group(function () {

Route::get('/clinical-records/{clinicalRecord}/print', [ClinicalRecordController::class, 'print'])
    ->name('clinical-records.print');
    
    // Solo admin con is_doctor o médicos pueden acceder
    Route::middleware(['role:admin,medico'])->group(function () {
        
        Route::resource('clinical-records', ClinicalRecordController::class);
        
        // Rutas adicionales para documentos
        Route::post('/clinical-records/{clinicalRecord}/documents', [ClinicalRecordController::class, 'uploadDocument'])
            ->name('clinical-records.upload-document');
            
        Route::delete('/clinical-records/documents/{documentId}', [ClinicalRecordController::class, 'deleteDocument'])
            ->name('clinical-records.delete-document');
            
        // API para obtener facturas pendientes de un paciente
        Route::get('/clinical-records/pending-invoices/{patientId}', [ClinicalRecordController::class, 'getPendingInvoices'])
            ->name('clinical-records.pending-invoices');
    });
});

use App\Http\Controllers\EcfSequenceController;

Route::middleware(['auth'])->group(function () {
    // Secuencias e-CF
    Route::resource('ecf-sequences', EcfSequenceController::class);
    Route::patch('ecf-sequences/{ecfSequence}/toggle', [EcfSequenceController::class, 'toggle'])
        ->name('ecf-sequences.toggle');
});




require __DIR__.'/auth.php';