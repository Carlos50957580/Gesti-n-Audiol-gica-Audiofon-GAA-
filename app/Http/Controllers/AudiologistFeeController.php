<?php
// app/Http/Controllers/AudiologistFeeController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Invoice;
use App\Models\AudiologistFee;
use App\Models\AudiologistFeeSetting;
use App\Models\AudiologistFeePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AudiologistFeeController extends Controller
{
    public function index(Request $request)
    {
        $query = AudiologistFee::with(['audiologist', 'invoice']);
        
        // Filtros
        if ($request->filled('audiologist_id')) {
            $query->where('audiologist_id', $request->audiologist_id);
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
        
        // ✅ CORREGIDO: Obtener audiólogos por role_id
        $audiologistRole = Role::where('name', 'audiologist')->orWhere('name', 'audiologo')->first();
        $audiologists = collect();
        
        if ($audiologistRole) {
            $audiologists = User::where('role_id', $audiologistRole->id)
                ->orderBy('name')
                ->get();
        }
        
        $stats = [
            'total_pending' => AudiologistFee::where('status', 'pending')->sum('fee_amount'),
            'total_paid' => AudiologistFee::where('status', 'paid')->sum('fee_amount'),
            'total_fees' => AudiologistFee::sum('fee_amount'),
            'audiologists_count' => $audiologists->count(),
        ];
        
        return view('audiologist-fees.index', compact('fees', 'audiologists', 'stats'));
    }
    
    public function calculateFee(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'audiologist_id' => 'required|exists:users,id',
            'invoice_total' => 'required|numeric|min:0',
        ]);
        
        $setting = AudiologistFeeSetting::where('audiologist_id', $request->audiologist_id)
            ->where('is_active', true)
            ->first();
            
        if (!$setting) {
            return response()->json([
                'error' => 'No hay configuración de honorarios activa para este audiólogo'
            ], 422);
        }
        
        $feeAmount = $setting->calculateFee($request->invoice_total);
        
        return response()->json([
            'calculation_type' => $setting->calculation_type,
            'calculation_value' => $setting->value,
            'fee_amount' => $feeAmount,
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'audiologist_id' => 'required|exists:users,id',
            'invoice_id' => 'required|exists:invoices,id',
            'invoice_total' => 'required|numeric|min:0',
            'calculation_type' => 'required|in:percentage,fixed',
            'calculation_value' => 'required|numeric|min:0',
            'fee_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        
        // Verificar si ya existe un fee para esta factura
        $exists = AudiologistFee::where('invoice_id', $request->invoice_id)->exists();
        if ($exists) {
            return response()->json([
                'message' => 'Ya existe un honorario registrado para esta factura'
            ], 422);
        }
        
        $fee = AudiologistFee::create([
            'audiologist_id' => $request->audiologist_id,
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
        $fee = AudiologistFee::findOrFail($id);
        
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
        $fee = AudiologistFee::findOrFail($id);
        
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
        $fee = AudiologistFee::where('invoice_id', $invoiceId)
            ->with(['audiologist', 'payments'])
            ->first();
            
        return response()->json($fee);
    }
}