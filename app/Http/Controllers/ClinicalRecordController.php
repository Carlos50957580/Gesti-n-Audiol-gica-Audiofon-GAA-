<?php

namespace App\Http\Controllers;

use App\Models\ClinicalRecord;
use App\Models\ClinicalRecordDocument;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Branch;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ClinicalRecordController extends Controller
{
    /**
     * Display a listing of clinical records.
     */
    /**
 * Display a listing of clinical records.
 */
public function index(Request $request)
{
    $user = auth()->user();
    $isAdmin = $user->role_id === 1 && $user->is_doctor == 1;
    $isDoctor = $user->role_id === 3;
    
    // Solo admin con is_doctor o médicos pueden acceder
    if (!$isAdmin && !$isDoctor) {
        abort(403, 'No tienes permiso para ver historias clínicas.');
    }
    
    // ============================================
    // FACTURAS PENDIENTES DE ATENCIÓN (TURNOS)
    // ============================================
    $pendingInvoices = Invoice::with(['patient', 'doctor', 'branch', 'items.service.category'])
        ->where('status', 'pagada')
        ->whereDoesntHave('clinicalRecord')
        ->get()
        ->filter(function($invoice) {
            return $invoice->requiresClinicalRecord();
        });
    
    // Si es médico, solo ver facturas donde él es el médico asignado
    if ($isDoctor) {
        $pendingInvoices = $pendingInvoices->filter(function($invoice) use ($user) {
            return $invoice->doctor_id == $user->id;
        });
    }
    
    // Ordenar por fecha de creación (más antiguas primero)
    $pendingInvoices = $pendingInvoices->sortBy('created_at');
    
    // ============================================
    // HISTORIAS CLÍNICAS COMPLETADAS
    // ============================================
    $query = ClinicalRecord::with(['patient', 'doctor', 'branch', 'invoice']);
    
    // Filtrar por doctor si es médico
    if ($isDoctor) {
        $query->where('doctor_id', $user->id);
    }
    
    // Filtros
    if ($request->filled('search')) {
        $search = $request->search;
        $query->whereHas('patient', function($q) use ($search) {
            $q->where('first_name', 'LIKE', "%{$search}%")
              ->orWhere('last_name', 'LIKE', "%{$search}%")
              ->orWhere('cedula', 'LIKE', "%{$search}%");
        });
    }
    
    if ($request->filled('patient_id')) {
        $query->where('patient_id', $request->patient_id);
    }
    
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    
    if ($request->filled('date_from')) {
        $query->whereDate('consultation_date', '>=', $request->date_from);
    }
    
    if ($request->filled('date_to')) {
        $query->whereDate('consultation_date', '<=', $request->date_to);
    }
    
    if ($request->filled('branch_id') && $isAdmin) {
        $query->where('branch_id', $request->branch_id);
    }
    
    $records = $query->latest('consultation_date')->paginate(15);
    
    $patients = Patient::orderBy('first_name')->get();
    $branches = $isAdmin ? Branch::where('is_active', 1)->get() : collect();
    
    return view('clinical-records.index', compact(
        'pendingInvoices', 'records', 'patients', 'branches', 'isAdmin'
    ));
}
    /**
     * Show the form for creating a new clinical record.
     */
    /**
 * Show the form for creating a new clinical record.
 */
/**
 * Show the form for creating a new clinical record.
 */
public function create(Request $request)
{
    $user = auth()->user();
    $isAdmin = $user->role_id === 1 && $user->is_doctor == 1;
    $isDoctor = $user->role_id === 3;
    
    if (!$isAdmin && !$isDoctor) {
        abort(403, 'No tienes permiso para crear historias clínicas.');
    }
    
    // Obtener facturas pagadas que requieren historia clínica y no la tienen
    $invoices = Invoice::with(['patient', 'items.service.category', 'doctor', 'branch'])
        ->where('status', 'pagada')
        ->whereDoesntHave('clinicalRecord')
        ->get()
        ->filter(function($invoice) {
            return $invoice->requiresClinicalRecord();
        });
    
    // Si es médico, solo ver facturas donde él es el médico asignado
    if ($isDoctor) {
        $invoices = $invoices->filter(function($invoice) use ($user) {
            return $invoice->doctor_id == $user->id;
        });
    }
    
    // Ordenar por fecha (más antiguas primero)
    $invoices = $invoices->sortBy('created_at');
    
    // Si se selecciona una factura específica
    $selectedInvoice = null;
    if ($request->has('invoice_id')) {
        $selectedInvoice = Invoice::with(['patient', 'doctor', 'branch'])
            ->find($request->invoice_id);
            
        if ($selectedInvoice) {
            // Verificar que el médico tenga acceso
            if ($isDoctor && $selectedInvoice->doctor_id != $user->id) {
                return redirect()->route('clinical-records.index')
                    ->with('error', 'No tienes permiso para crear historia clínica de esta factura.');
            }
            
            if (!$selectedInvoice->requiresClinicalRecord()) {
                return redirect()->route('clinical-records.index')
                    ->with('error', 'Esta factura no requiere historia clínica.');
            }
            
            if ($selectedInvoice->hasClinicalRecord()) {
                return redirect()->route('clinical-records.index')
                    ->with('error', 'Esta factura ya tiene una historia clínica asociada.');
            }
        }
    }
    
    // Obtener doctores disponibles
    $doctors = User::where(function($q) {
        $q->where('role_id', 3)
          ->orWhere(function($q2) {
              $q2->where('role_id', 1)->where('is_doctor', 1);
          });
    })
    ->where('is_active', 1)
    ->orderBy('name')
    ->get();
    
    $patients = Patient::orderBy('first_name')->get();
    $branches = Branch::where('is_active', 1)->get();
    
    return view('clinical-records.create', compact(
        'invoices', 'selectedInvoice', 'doctors', 'patients', 'branches'
    ));
}
    /**
     * Store a newly created clinical record.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->role_id === 1 && $user->is_doctor == 1;
        $isDoctor = $user->role_id === 3;
        
        if (!$isAdmin && !$isDoctor) {
            abort(403, 'No tienes permiso para crear historias clínicas.');
        }

        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required|exists:invoices,id',
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'branch_id' => 'required|exists:branches,id',
            'consultation_date' => 'required|date',
            'consultation_type' => 'required|in:primera_vez,seguimiento,urgencia,control',
            'consultation_reason' => 'nullable|string|max:255',
            'reason_for_consultation' => 'nullable|string',
            'anamnesis' => 'nullable|string',
            'physical_exam' => 'nullable|string',
            'presumptive_diagnosis' => 'nullable|string',
            'treatment' => 'nullable|string',
            'evolution' => 'nullable|string',
            'observations' => 'nullable|string',
            'recommendations' => 'nullable|string',
            
            // Signos vitales
            'blood_pressure_systolic' => 'nullable|numeric|min:0|max:300',
            'blood_pressure_diastolic' => 'nullable|numeric|min:0|max:200',
            'heart_rate' => 'nullable|numeric|min:0|max:300',
            'respiratory_rate' => 'nullable|numeric|min:0|max:100',
            'oxygen_saturation' => 'nullable|numeric|min:0|max:100',
            'temperature' => 'nullable|numeric|min:0|max:50',
            'weight' => 'nullable|numeric|min:0|max:500',
            'height' => 'nullable|numeric|min:0|max:300',
            'fetal_heart_rate' => 'nullable|numeric|min:0|max:300',
            'uterine_height' => 'nullable|numeric|min:0|max:50',
            'edema' => 'nullable|string|max:255',
            'fetal_movements' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Verificar que la factura no tenga ya una historia clínica
        $invoice = Invoice::with('clinicalRecord')->find($request->invoice_id);
        if ($invoice->clinicalRecord) {
            return redirect()->back()
                ->with('error', 'Esta factura ya tiene una historia clínica asociada.')
                ->withInput();
        }

        // Verificar que la factura requiera historia clínica
        if (!$invoice->requiresClinicalRecord()) {
            return redirect()->back()
                ->with('error', 'Esta factura no requiere historia clínica.')
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Preparar signos vitales
            $vitalSigns = [
                'blood_pressure_systolic' => $request->blood_pressure_systolic,
                'blood_pressure_diastolic' => $request->blood_pressure_diastolic,
                'heart_rate' => $request->heart_rate,
                'respiratory_rate' => $request->respiratory_rate,
                'oxygen_saturation' => $request->oxygen_saturation,
                'temperature' => $request->temperature,
                'weight' => $request->weight,
                'height' => $request->height,
                'bmi' => $this->calculateBMI($request->weight, $request->height),
                'fetal_heart_rate' => $request->fetal_heart_rate,
                'uterine_height' => $request->uterine_height,
                'edema' => $request->edema,
                'fetal_movements' => $request->fetal_movements,
            ];

            // Crear historia clínica
            $clinicalRecord = ClinicalRecord::create([
                'invoice_id' => $request->invoice_id,
                'patient_id' => $request->patient_id,
                'doctor_id' => $request->doctor_id,
                'branch_id' => $request->branch_id,
                'consultation_date' => $request->consultation_date,
                'consultation_type' => $request->consultation_type,
                'consultation_reason' => $request->consultation_reason,
                'reason_for_consultation' => $request->reason_for_consultation,
                'anamnesis' => $request->anamnesis,
                'vital_signs' => $vitalSigns,
                'physical_exam' => $request->physical_exam,
                'presumptive_diagnosis' => $request->presumptive_diagnosis,
                'treatment' => $request->treatment,
                'evolution' => $request->evolution,
                'observations' => $request->observations,
                'recommendations' => $request->recommendations,
                'status' => 'completada',
            ]);

            DB::commit();

            return redirect()
                ->route('clinical-records.show', $clinicalRecord)
                ->with('success', 'Historia clínica creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al crear la historia clínica: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified clinical record.
     */
    public function show(ClinicalRecord $clinicalRecord)
    {
        $user = auth()->user();
        $isAdmin = $user->role_id === 1 && $user->is_doctor == 1;
        $isDoctor = $user->role_id === 3;
        
        if (!$isAdmin && !$isDoctor) {
            abort(403, 'No tienes permiso para ver esta historia clínica.');
        }
        
        if ($isDoctor && $clinicalRecord->doctor_id !== $user->id) {
            abort(403, 'No tienes permiso para ver esta historia clínica.');
        }
        
        $clinicalRecord->load(['patient', 'doctor', 'branch', 'invoice', 'documents.uploader']);
        
        $company = [
            'name' => Setting::get('company_name', 'Mi Clínica'),
            'logo' => Setting::get('company_logo', null),
        ];
        
        return view('clinical-records.show', compact('clinicalRecord', 'company'));
    }

    /**
     * Show the form for editing the specified clinical record.
     */
    public function edit(ClinicalRecord $clinicalRecord)
    {
        $user = auth()->user();
        $isAdmin = $user->role_id === 1 && $user->is_doctor == 1;
        $isDoctor = $user->role_id === 3;
        
        if (!$isAdmin && !$isDoctor) {
            abort(403, 'No tienes permiso para editar esta historia clínica.');
        }
        
        if ($isDoctor && $clinicalRecord->doctor_id !== $user->id) {
            abort(403, 'No tienes permiso para editar esta historia clínica.');
        }
        
        if (!$clinicalRecord->canBeEdited()) {
            return redirect()->route('clinical-records.show', $clinicalRecord)
                ->with('error', 'Esta historia clínica no puede ser editada porque ya está completada.');
        }
        
        $clinicalRecord->load(['patient', 'doctor', 'branch', 'invoice']);
        
        $doctors = User::where(function($q) {
            $q->where('role_id', 3)
              ->orWhere(function($q2) {
                  $q2->where('role_id', 1)->where('is_doctor', 1);
              });
        })
        ->where('is_active', 1)
        ->orderBy('name')
        ->get();
        
        $branches = Branch::where('is_active', 1)->get();
        
        return view('clinical-records.edit', compact('clinicalRecord', 'doctors', 'branches'));
    }

    /**
     * Update the specified clinical record.
     */
    public function update(Request $request, ClinicalRecord $clinicalRecord)
    {
        $user = auth()->user();
        $isAdmin = $user->role_id === 1 && $user->is_doctor == 1;
        $isDoctor = $user->role_id === 3;
        
        if (!$isAdmin && !$isDoctor) {
            abort(403, 'No tienes permiso para editar esta historia clínica.');
        }
        
        if ($isDoctor && $clinicalRecord->doctor_id !== $user->id) {
            abort(403, 'No tienes permiso para editar esta historia clínica.');
        }
        
        if (!$clinicalRecord->canBeEdited()) {
            return redirect()->back()
                ->with('error', 'Esta historia clínica no puede ser editada.')
                ->withInput();
        }

        $validator = Validator::make($request->all(), [
            'consultation_date' => 'required|date',
            'consultation_type' => 'required|in:primera_vez,seguimiento,urgencia,control',
            'consultation_reason' => 'nullable|string|max:255',
            'reason_for_consultation' => 'nullable|string',
            'anamnesis' => 'nullable|string',
            'physical_exam' => 'nullable|string',
            'presumptive_diagnosis' => 'nullable|string',
            'treatment' => 'nullable|string',
            'evolution' => 'nullable|string',
            'observations' => 'nullable|string',
            'recommendations' => 'nullable|string',
            
            // Signos vitales
            'blood_pressure_systolic' => 'nullable|numeric|min:0|max:300',
            'blood_pressure_diastolic' => 'nullable|numeric|min:0|max:200',
            'heart_rate' => 'nullable|numeric|min:0|max:300',
            'respiratory_rate' => 'nullable|numeric|min:0|max:100',
            'oxygen_saturation' => 'nullable|numeric|min:0|max:100',
            'temperature' => 'nullable|numeric|min:0|max:50',
            'weight' => 'nullable|numeric|min:0|max:500',
            'height' => 'nullable|numeric|min:0|max:300',
            'fetal_heart_rate' => 'nullable|numeric|min:0|max:300',
            'uterine_height' => 'nullable|numeric|min:0|max:50',
            'edema' => 'nullable|string|max:255',
            'fetal_movements' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Preparar signos vitales
            $vitalSigns = [
                'blood_pressure_systolic' => $request->blood_pressure_systolic,
                'blood_pressure_diastolic' => $request->blood_pressure_diastolic,
                'heart_rate' => $request->heart_rate,
                'respiratory_rate' => $request->respiratory_rate,
                'oxygen_saturation' => $request->oxygen_saturation,
                'temperature' => $request->temperature,
                'weight' => $request->weight,
                'height' => $request->height,
                'bmi' => $this->calculateBMI($request->weight, $request->height),
                'fetal_heart_rate' => $request->fetal_heart_rate,
                'uterine_height' => $request->uterine_height,
                'edema' => $request->edema,
                'fetal_movements' => $request->fetal_movements,
            ];

            // Actualizar historia clínica
            $clinicalRecord->update([
                'consultation_date' => $request->consultation_date,
                'consultation_type' => $request->consultation_type,
                'consultation_reason' => $request->consultation_reason,
                'reason_for_consultation' => $request->reason_for_consultation,
                'anamnesis' => $request->anamnesis,
                'vital_signs' => $vitalSigns,
                'physical_exam' => $request->physical_exam,
                'presumptive_diagnosis' => $request->presumptive_diagnosis,
                'treatment' => $request->treatment,
                'evolution' => $request->evolution,
                'observations' => $request->observations,
                'recommendations' => $request->recommendations,
            ]);

            DB::commit();

            return redirect()
                ->route('clinical-records.show', $clinicalRecord)
                ->with('success', 'Historia clínica actualizada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar la historia clínica: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified clinical record.
     */
    public function destroy(ClinicalRecord $clinicalRecord)
    {
        $user = auth()->user();
        $isAdmin = $user->role_id === 1 && $user->is_doctor == 1;
        
        if (!$isAdmin) {
            abort(403, 'No tienes permiso para eliminar historias clínicas.');
        }
        
        DB::beginTransaction();
        
        try {
            // Eliminar documentos asociados
            foreach ($clinicalRecord->documents as $document) {
                if (Storage::disk('public')->exists($document->file_path)) {
                    Storage::disk('public')->delete($document->file_path);
                }
                $document->delete();
            }
            
            $clinicalRecord->delete();
            
            DB::commit();
            
            return redirect()
                ->route('clinical-records.index')
                ->with('success', 'Historia clínica eliminada exitosamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al eliminar la historia clínica: ' . $e->getMessage());
        }
    }

    /**
     * Upload documents for a clinical record.
     */
    public function uploadDocument(Request $request, ClinicalRecord $clinicalRecord)
    {
        $validator = Validator::make($request->all(), [
            'document' => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('document');
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $fileType = $file->getMimeType();
            
            $path = $file->storeAs(
                'clinical-records/' . $clinicalRecord->id,
                time() . '_' . $fileName,
                'public'
            );

            $document = $clinicalRecord->documents()->create([
                'patient_id' => $clinicalRecord->patient_id,
                'name' => $request->name ?? $fileName,
                'file_path' => $path,
                'file_name' => $fileName,
                'file_type' => $fileType,
                'file_size' => $fileSize,
                'mime_type' => $fileType,
                'uploaded_by' => auth()->id(),
                'description' => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'document' => [
                    'id' => $document->id,
                    'name' => $document->name,
                    'file_name' => $document->file_name,
                    'file_size_formatted' => $document->file_size_formatted,
                    'file_url' => $document->file_url,
                    'description' => $document->description,
                    'created_at' => $document->created_at->format('d/m/Y H:i'),
                    'uploader_name' => $document->uploader->name ?? 'Sistema',
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir el documento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a document.
     */
    public function deleteDocument($documentId)
    {
        $document = ClinicalRecordDocument::findOrFail($documentId);
        
        $user = auth()->user();
        $isAdmin = $user->role_id === 1 && $user->is_doctor == 1;
        $isDoctor = $user->role_id === 3;
        
        if (!$isAdmin && !$isDoctor) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar este documento.'
            ], 403);
        }
        
        if ($isDoctor && $document->clinicalRecord->doctor_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar este documento.'
            ], 403);
        }

        try {
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            
            $document->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Documento eliminado exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el documento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate BMI.
     */
    private function calculateBMI($weight, $height)
    {
        if ($weight && $height && $height > 0) {
            $heightInMeters = $height / 100;
            return round($weight / ($heightInMeters * $heightInMeters), 2);
        }
        return null;
    }

    /**
     * Get invoices that require clinical record for a patient.
     */
    public function getPendingInvoices($patientId)
    {
        $invoices = Invoice::with(['items.service.category'])
            ->where('patient_id', $patientId)
            ->where('status', 'pagada')
            ->whereDoesntHave('clinicalRecord')
            ->get()
            ->filter(function($invoice) {
                return $invoice->requiresClinicalRecord();
            })
            ->map(function($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'total' => $invoice->total,
                    'date' => $invoice->created_at->format('d/m/Y'),
                ];
            });
            
        return response()->json($invoices);
    }

    /**
 * Print the specified clinical record.
 */
public function print(ClinicalRecord $clinicalRecord)
{
    $user = auth()->user();
    $isAdmin = $user->role_id === 1 && $user->is_doctor == 1;
    $isDoctor = $user->role_id === 3;
    
    if (!$isAdmin && !$isDoctor) {
        abort(403, 'No tienes permiso para imprimir esta historia clínica.');
    }
    
    if ($isDoctor && $clinicalRecord->doctor_id !== $user->id) {
        abort(403, 'No tienes permiso para imprimir esta historia clínica.');
    }
    
    $clinicalRecord->load(['patient', 'doctor', 'branch', 'invoice', 'documents']);
    
    // Obtener datos de la empresa
    $company = [
        'name' => Setting::get('company_name', 'SAP PROSAUD'),
        'business_name' => Setting::get('company_business_name', 'SAP PROSAUD SRL'),
        'rnc' => Setting::get('company_rnc', ''),
        'email' => Setting::get('company_email', ''),
        'phone' => Setting::get('company_phone', ''),
        'mobile' => Setting::get('company_mobile', ''),
        'address' => Setting::get('company_address', ''),
        'slogan' => Setting::get('company_slogan', ''),
        'website' => Setting::get('company_website', ''),
        'footer_text' => Setting::get('company_footer_text', 'Gracias por confiar en nosotros'),
        'currency' => Setting::get('company_currency', 'DOP'),
    ];
    
    return view('clinical-records.print', compact('clinicalRecord', 'company'));
}
}