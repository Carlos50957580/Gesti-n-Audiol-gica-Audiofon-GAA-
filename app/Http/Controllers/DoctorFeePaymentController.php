<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DoctorFee;
use App\Models\DoctorFeePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoctorFeePaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = DoctorFeePayment::with('doctor');

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(15);

        // Obtener médicos (usuarios con is_doctor = 1)
        $doctors = User::where('is_doctor', 1)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        return view('doctor-fees.payments', compact('payments', 'doctors'));
    }

    public function getPendingFees($doctorId)
    {
        $pendingFees = DoctorFee::where('doctor_id', $doctorId)
            ->where('status', 'pending')
            ->with('invoice')
            ->get();

        $result = $pendingFees->map(function ($fee) {
            return [
                'id' => $fee->id,
                'invoice_id' => $fee->invoice_id,
                'doctor_id' => $fee->doctor_id,
                'invoice_total' => $fee->invoice_total,
                'calculation_type' => $fee->calculation_type,
                'calculation_value' => $fee->calculation_value,
                'fee_amount' => $fee->fee_amount,
                'status' => $fee->status,
                'payment_date' => $fee->payment_date,
                'notes' => $fee->notes,
                'created_at' => $fee->created_at,
                'updated_at' => $fee->updated_at,
                'paid_amount' => $fee->paid_amount,
                'remaining_amount' => $fee->remaining_amount,
                'invoice' => $fee->invoice,
            ];
        });

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:bank_transfer,cash,check,other',
            'reference_number' => 'nullable|string|max:100',
            'fee_ids' => 'required|array|min:1',
            'fee_ids.*' => 'exists:doctor_fees,id',
            'amounts' => 'required|array|min:1',
            'amounts.*' => 'numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $totalApplied = array_sum($request->amounts);

            if ($totalApplied != $request->amount) {
                throw new \Exception('La suma de los montos no coincide con el pago total');
            }

            $payment = DoctorFeePayment::create([
                'doctor_id' => $request->doctor_id,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'reference_number' => $request->reference_number,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
            ]);

            foreach ($request->fee_ids as $index => $feeId) {
                $amount = $request->amounts[$index];

                if ($amount > 0) {
                    $fee = DoctorFee::findOrFail($feeId);

                    if ($fee->doctor_id != $request->doctor_id) {
                        throw new \Exception('Fee no pertenece al médico seleccionado');
                    }

                    $payment->fees()->attach($feeId, ['amount_applied' => $amount]);

                    if ($fee->remaining_amount <= $amount) {
                        $fee->update([
                            'status' => 'paid',
                            'payment_date' => $request->payment_date,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Pago registrado correctamente',
                'payment' => $payment
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error al registrar el pago: ' . $e->getMessage()
            ], 422);
        }
    }

    public function show($id)
    {
        $payment = DoctorFeePayment::with([
            'doctor',
            'fees.invoice.patient',
            'fees.invoice.items.service',
            'fees.invoice.branch',
            'fees.invoice.user'
        ])->findOrFail($id);

        $feesData = $payment->fees->map(function ($fee) {
            $invoice = $fee->invoice;
            $services = $invoice->items->map(function ($item) {
                return [
                    'service_name' => $item->service->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->subtotal,
                    'insurance_amount' => $item->insurance_amount,
                    'patient_amount' => $item->patient_amount,
                ];
            });

            return [
                'id' => $fee->id,
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'fee_amount' => $fee->fee_amount,
                'amount_applied' => $fee->pivot->amount_applied,
                'invoice_total' => $invoice->total,
                'subtotal' => $invoice->subtotal,
                'insurance_discount' => $invoice->insurance_discount,
                'patient' => [
                    'name' => $invoice->patient->first_name . ' ' . $invoice->patient->last_name,
                    'cedula' => $invoice->patient->cedula,
                    'phone' => $invoice->patient->phone,
                ],
                'services' => $services,
                'branch' => $invoice->branch->name ?? 'N/A',
                'created_by' => $invoice->user->name ?? 'N/A',
                'created_at' => $invoice->created_at->format('d/m/Y H:i'),
            ];
        });

        return response()->json([
            'id' => $payment->id,
            'amount' => $payment->amount,
            'payment_date' => $payment->payment_date->format('d/m/Y'),
            'payment_method' => $payment->payment_method,
            'reference_number' => $payment->reference_number,
            'notes' => $payment->notes,
            'created_at' => $payment->created_at->format('d/m/Y H:i'),
            'doctor' => [
                'name' => $payment->doctor->name,
            ],
            'fees' => $feesData,
        ]);
    }

    public function destroy($id)
    {
        $payment = DoctorFeePayment::findOrFail($id);

        DB::beginTransaction();

        try {
            foreach ($payment->fees as $fee) {
                $fee->update([
                    'status' => 'pending',
                    'payment_date' => null,
                ]);
            }

            $payment->delete();

            DB::commit();

            return response()->json([
                'message' => 'Pago eliminado correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error al eliminar el pago'
            ], 422);
        }
    }
}