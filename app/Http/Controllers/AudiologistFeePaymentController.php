<?php
// app/Http/Controllers/AudiologistFeePaymentController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\AudiologistFee;
use App\Models\AudiologistFeePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AudiologistFeePaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = AudiologistFeePayment::with('audiologist');
        
        if ($request->filled('audiologist_id')) {
            $query->where('audiologist_id', $request->audiologist_id);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }
        
        $payments = $query->orderBy('payment_date', 'desc')->paginate(15);
        
        // ✅ CORREGIDO: Obtener audiólogos por role_id
        $audiologistRole = Role::where('name', 'audiologist')->orWhere('name', 'audiologo')->first();
        $audiologists = collect();
        
        if ($audiologistRole) {
            $audiologists = User::where('role_id', $audiologistRole->id)
                ->orderBy('name')
                ->get();
        }
            
        return view('audiologist-fees.payments', compact('payments', 'audiologists'));
    }
    
   public function getPendingFees($audiologistId)
{
    $pendingFees = AudiologistFee::where('audiologist_id', $audiologistId)
        ->where('status', 'pending')
        ->with('invoice')
        ->get();
    
    // Convertir a array y agregar el campo remaining_amount explícitamente
    $result = $pendingFees->map(function ($fee) {
        return [
            'id' => $fee->id,
            'invoice_id' => $fee->invoice_id,
            'audiologist_id' => $fee->audiologist_id,
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
            'remaining_amount' => $fee->remaining_amount, // ← Esto es clave
            'invoice' => $fee->invoice,
        ];
    });
    
    return response()->json($result);
}
    
    public function store(Request $request)
    {
        $request->validate([
            'audiologist_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:bank_transfer,cash,check,other',
            'reference_number' => 'nullable|string|max:100',
            'fee_ids' => 'required|array|min:1',
            'fee_ids.*' => 'exists:audiologist_fees,id',
            'amounts' => 'required|array|min:1',
            'amounts.*' => 'numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Calcular total de los montos aplicados
            $totalApplied = array_sum($request->amounts);
            
            if ($totalApplied != $request->amount) {
                throw new \Exception('La suma de los montos no coincide con el pago total');
            }
            
            // Crear el pago
            $payment = AudiologistFeePayment::create([
                'audiologist_id' => $request->audiologist_id,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'reference_number' => $request->reference_number,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
            ]);
            
            // Registrar los detalles y actualizar los fees
            foreach ($request->fee_ids as $index => $feeId) {
                $amount = $request->amounts[$index];
                
                if ($amount > 0) {
                    $fee = AudiologistFee::findOrFail($feeId);
                    
                    // Verificar que el fee pertenezca al audiólogo
                    if ($fee->audiologist_id != $request->audiologist_id) {
                        throw new \Exception('Fee no pertenece al audiólogo seleccionado');
                    }
                    
                    $payment->fees()->attach($feeId, ['amount_applied' => $amount]);
                    
                    // Si el fee queda totalmente pagado, actualizar su estado
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
        $payment = AudiologistFeePayment::with(['audiologist', 'fees.invoice'])->findOrFail($id);
        
        return response()->json($payment);
    }
    
    public function destroy($id)
    {
        $payment = AudiologistFeePayment::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            // Revertir el estado de los fees asociados
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