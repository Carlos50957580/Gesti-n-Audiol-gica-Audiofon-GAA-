<?php

namespace App\Http\Controllers;

use App\Models\ClinicalRecord;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClinicalRecordController extends Controller
{
    /**
     * Lista facturas PAGADAS asignadas al audiólogo autenticado
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['patient', 'branch', 'clinicalRecord'])
            ->where('audiologist_id', auth()->id())
            ->where('status', 'pagada');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name',  'like', "%{$search}%")
                  ->orWhere('cedula',     'like', "%{$search}%");
            });
        }

        if ($request->filled('hc_status')) {
            if ($request->hc_status === 'pendiente') {
                // Facturas sin HC o con HC pendiente
                $query->where(function ($q) {
                    $q->doesntHave('clinicalRecord')
                      ->orWhereHas('clinicalRecord', fn($q) => $q->where('status', 'pendiente'));
                });
            } elseif ($request->hc_status === 'completada') {
                $query->whereHas('clinicalRecord', fn($q) => $q->where('status', 'completada'));
            }
        }

        $invoices = $query->latest()->paginate(10)->withQueryString();

        return view('clinical_records.index', compact('invoices'));
    }

    /**
     * Formulario para llenar la HC de una factura
     * Si no existe HC la crea automáticamente aquí
     */
    public function edit(Invoice $invoice)
    {
        abort_if($invoice->audiologist_id !== auth()->id(), 403);
        abort_if($invoice->status !== 'pagada', 403);

        $invoice->load(['patient.insurance', 'items.service', 'branch']);

        // Crear HC si no existe — sin Observer, aquí mismo
        $clinicalRecord = ClinicalRecord::firstOrCreate(
            ['invoice_id' => $invoice->id],
            [
                'patient_id'     => $invoice->patient_id,
                'audiologist_id' => $invoice->audiologist_id,
                'branch_id'      => $invoice->branch_id,
                'status'         => 'pendiente',
            ]
        );

        return view('clinical_records.edit', compact('invoice', 'clinicalRecord'));
    }

    /**
     * Guardar la HC
     */
    public function update(Request $request, Invoice $invoice)
    {
        abort_if($invoice->audiologist_id !== auth()->id(), 403);

        $request->validate([
            'reason_for_consultation' => 'nullable|string|max:2000',
            'diagnosis'               => 'nullable|string|max:2000',
            'treatment_plan'          => 'nullable|string|max:2000',
            'notes'                   => 'nullable|string|max:1000',
            'status'                  => 'required|in:pendiente,completada',
        ]);

        ClinicalRecord::updateOrCreate(
            ['invoice_id' => $invoice->id],
            [
                'patient_id'              => $invoice->patient_id,
                'audiologist_id'          => $invoice->audiologist_id,
                'branch_id'               => $invoice->branch_id,
                'reason_for_consultation' => $request->reason_for_consultation,
                'diagnosis'               => $request->diagnosis,
                'treatment_plan'          => $request->treatment_plan,
                'notes'                   => $request->notes,
                'status'                  => $request->status,
            ]
        );

        return redirect()->route('clinical-records.index')
            ->with('success', 'Historia clínica actualizada correctamente.');
    }

    /**
     * Ver HC completada (solo lectura)
     */
    public function show(Invoice $invoice)
    {
        abort_if($invoice->audiologist_id !== auth()->id(), 403);

        $invoice->load(['patient.insurance', 'items.service', 'branch', 'clinicalRecord']);

        abort_if(!$invoice->clinicalRecord, 404);

        $clinicalRecord = $invoice->clinicalRecord;

        return view('clinical_records.show', compact('invoice', 'clinicalRecord'));
    }

    /**
     * JSON — historial de todas las facturas pagadas de un paciente
     * para este audiólogo
     */
    public function patientHistory(int $patientId): JsonResponse
    {
        $invoices = Invoice::with('clinicalRecord')
            ->where('patient_id',     $patientId)
            ->where('audiologist_id', auth()->id())
            ->where('status',         'pagada')
            ->latest()
            ->get();

        abort_if($invoices->isEmpty(), 403);

        $patient = $invoices->first()->patient ?? \App\Models\Patient::find($patientId);

        return response()->json([
            'patient_name'   => $patient->first_name . ' ' . $patient->last_name,
            'patient_cedula' => $patient->cedula,
            'records'        => $invoices->map(fn($inv) => [
                'invoice_number' => 'FAC-' . str_pad($inv->id, 6, '0', STR_PAD_LEFT),
                'status'         => $inv->clinicalRecord?->status ?? 'pendiente',
                'diagnosis'      => $inv->clinicalRecord?->diagnosis,
                'date'           => $inv->updated_at->format('d/m/Y H:i'),
                'edit_url'       => route('clinical-records.edit', $inv->id),
            ]),
        ]);
    }

  
public function showData(Invoice $invoice): JsonResponse
{
    abort_if($invoice->audiologist_id !== auth()->id(), 403);

    $invoice->load(['patient', 'branch', 'clinicalRecord']);

    $hc = $invoice->clinicalRecord;

    return response()->json([
        'patient_name'            => $invoice->patient->first_name . ' ' . $invoice->patient->last_name,
        'patient_cedula'          => $invoice->patient->cedula,
        'invoice_number'          => 'FAC-' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT),
        'branch'                  => $invoice->branch->name,
        'date'                    => $invoice->updated_at->format('d/m/Y'),
        'hc_status'               => $hc?->status ?? 'pendiente',
        'reason_for_consultation' => $hc?->reason_for_consultation,
        'diagnosis'               => $hc?->diagnosis,
        'treatment_plan'          => $hc?->treatment_plan,
        'notes'                   => $hc?->notes,
        'updated_at'              => $hc?->updated_at?->format('d/m/Y H:i') ?? '—',
    ]);
}
}