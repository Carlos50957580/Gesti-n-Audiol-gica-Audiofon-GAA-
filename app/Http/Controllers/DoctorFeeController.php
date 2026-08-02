<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DoctorFee;
use App\Models\DoctorFeeSetting;
use App\Models\DoctorFeePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoctorFeeController extends Controller
{
    public function index(Request $request)
    {
        $query = DoctorFee::with(['doctor', 'invoice']);

        // Filtros
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $fees = $query->orderBy('created_at', 'desc')->paginate(15);

        // Obtener médicos (usuarios con is_doctor = 1)
        $doctors = User::where('is_doctor', 1)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        $stats = [
            'total_pending' => DoctorFee::where('status', 'pending')->sum('fee_amount'),
            'total_paid' => DoctorFee::where('status', 'paid')->sum('fee_amount'),
            'total_fees' => DoctorFee::sum('fee_amount'),
            'doctors_count' => $doctors->count(),
        ];

        return view('doctor-fees.index', compact('fees', 'doctors', 'stats'));
    }

    public function show($id)
    {
        $fee = DoctorFee::with([
            'doctor',
            'payments',
            'invoice.patient',
            'invoice.items.service',
            'invoice.branch',
            'invoice.user'
        ])->findOrFail($id);

        // Obtener los servicios de la factura
        $services = $fee->invoice->items->map(function ($item) {
            return [
                'service_name' => $item->service->name,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'subtotal' => $item->subtotal,
                'coverage_percentage' => $item->coverage_percentage,
                'insurance_amount' => $item->insurance_amount,
                'patient_amount' => $item->patient_amount,
            ];
        });

        return response()->json([
            'id' => $fee->id,
            'doctor' => $fee->doctor,
            'invoice' => $fee->invoice,
            'invoice_total' => $fee->invoice_total,
            'calculation_type' => $fee->calculation_type,
            'calculation_value' => $fee->calculation_value,
            'fee_amount' => $fee->fee_amount,
            'status' => $fee->status,
            'payment_date' => $fee->payment_date,
            'notes' => $fee->notes,
            'created_at' => $fee->created_at,
            'payments' => $fee->payments,
            'patient' => $fee->invoice->patient,
            'services' => $services,
            'branch' => $fee->invoice->branch,
            'created_by' => $fee->invoice->user,
        ]);
    }

    public function calculateFee(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'doctor_id' => 'required|exists:users,id',
            'invoice_total' => 'required|numeric|min:0',
            'service_id' => 'nullable|exists:services,id',
        ]);

        // Obtener la configuración con prioridad
        $setting = DoctorFeeSetting::getForDoctorAndService(
            $request->doctor_id,
            $request->service_id
        );

        if (!$setting) {
            return response()->json([
                'error' => 'No hay configuración de honorarios activa para este médico y servicio'
            ], 422);
        }

        $feeAmount = $setting->calculateFee($request->invoice_total);

        return response()->json([
            'calculation_type' => $setting->calculation_type,
            'calculation_value' => $setting->value,
            'fee_amount' => $feeAmount,
            'scope' => $setting->scope_description,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'invoice_id' => 'required|exists:invoices,id',
            'invoice_total' => 'required|numeric|min:0',
            'calculation_type' => 'required|in:percentage,fixed',
            'calculation_value' => 'required|numeric|min:0',
            'fee_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Verificar si ya existe un fee para esta factura
        $exists = DoctorFee::where('invoice_id', $request->invoice_id)->exists();
        if ($exists) {
            return response()->json([
                'message' => 'Ya existe un honorario registrado para esta factura'
            ], 422);
        }

        $fee = DoctorFee::create([
            'doctor_id' => $request->doctor_id,
            'invoice_id' => $request->invoice_id,
            'invoice_total' => $request->invoice_total,
            'calculation_type' => $request->calculation_type,
            'calculation_value' => $request->calculation_value,
            'fee_amount' => $request->fee_amount,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Honorario registrado correctamente',
            'fee' => $fee
        ]);
    }

    public function update(Request $request, $id)
    {
        $fee = DoctorFee::findOrFail($id);

        $request->validate([
            'status' => 'in:pending,paid,cancelled',
            'payment_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $fee->update($request->only(['status', 'payment_date', 'notes']));

        return response()->json([
            'message' => 'Honorario actualizado correctamente',
            'fee' => $fee
        ]);
    }

    public function destroy($id)
    {
        $fee = DoctorFee::findOrFail($id);

        if ($fee->status === 'paid') {
            return response()->json([
                'message' => 'No se puede eliminar un honorario que ya ha sido pagado'
            ], 422);
        }

        $fee->delete();

        return response()->json([
            'message' => 'Honorario eliminado correctamente'
        ]);
    }

    public function getInvoiceFees($invoiceId)
    {
        $fee = DoctorFee::where('invoice_id', $invoiceId)
            ->with(['doctor', 'payments'])
            ->first();

        return response()->json($fee);
    }
}