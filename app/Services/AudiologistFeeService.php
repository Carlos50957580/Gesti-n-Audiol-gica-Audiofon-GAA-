<?php
// app/Services/AudiologistFeeService.php

namespace App\Services;

use App\Models\Invoice;
use App\Models\AudiologistFee;
use App\Models\AudiologistFeeSetting;

class AudiologistFeeService
{
    /**
     * Calcular y crear automáticamente el honorario para una factura
     */
    public function calculateAndCreateFee(Invoice $invoice)
    {
        // Verificar que la factura tenga un audiólogo asignado
        if (!$invoice->audiologist_id) {
            \Illuminate\Support\Facades\Log::info('No se creó honorario: la factura no tiene audiólogo asignado', [
                'invoice_id' => $invoice->id
            ]);
            return null;
        }
        
        // Buscar la configuración de honorarios del audiólogo
        $setting = AudiologistFeeSetting::where('audiologist_id', $invoice->audiologist_id)
            ->where('is_active', true)
            ->first();
        
        if (!$setting) {
            \Illuminate\Support\Facades\Log::info('No se creó honorario: el audiólogo no tiene configuración activa', [
                'invoice_id' => $invoice->id,
                'audiologist_id' => $invoice->audiologist_id
            ]);
            return null;
        }
        
        // Verificar si ya existe un honorario para esta factura
        $existingFee = AudiologistFee::where('invoice_id', $invoice->id)->first();
        if ($existingFee) {
            return $existingFee;
        }
        
        // 🔥 CORREGIDO: Calcular el honorario sobre el SUBTOTAL, no sobre el total
        // El subtotal es el valor antes del descuento del seguro
        $baseAmount = $invoice->subtotal;
        $feeAmount = $setting->calculateFee($baseAmount);
        
        \Illuminate\Support\Facades\Log::info('Calculando honorario', [
            'invoice_id' => $invoice->id,
            'subtotal' => $baseAmount,
            'total_con_descuento' => $invoice->total,
            'descuento_seguro' => $invoice->insurance_discount,
            'tipo_calculo' => $setting->calculation_type,
            'valor_calculo' => $setting->value,
            'honorario_calculado' => $feeAmount
        ]);
        
        // Crear el registro de honorario
        $fee = AudiologistFee::create([
            'audiologist_id' => $invoice->audiologist_id,
            'invoice_id' => $invoice->id,
            'invoice_total' => $baseAmount, // Guardamos el subtotal como base
            'calculation_type' => $setting->calculation_type,
            'calculation_value' => $setting->value,
            'fee_amount' => $feeAmount,
            'status' => 'pending',
            'notes' => 'Generado automáticamente desde la factura #' . $invoice->id . ' (calculado sobre subtotal: ' . number_format($baseAmount, 2) . ')',
        ]);
        
        \Illuminate\Support\Facades\Log::info('Honorario creado exitosamente', [
            'fee_id' => $fee->id,
            'monto_honorario' => $feeAmount
        ]);
        
        return $fee;
    }
    
    /**
     * Recalcular honorario para una factura específica (útil si se edita la factura)
     */
    public function recalculateFee(Invoice $invoice)
    {
        $fee = AudiologistFee::where('invoice_id', $invoice->id)->first();
        
        if (!$fee) {
            return $this->calculateAndCreateFee($invoice);
        }
        
        // Solo recalcular si está pendiente
        if ($fee->status !== 'pending') {
            return $fee;
        }
        
        $setting = AudiologistFeeSetting::where('audiologist_id', $invoice->audiologist_id)
            ->where('is_active', true)
            ->first();
        
        if (!$setting) {
            return null;
        }
        
        // Recalcular sobre el subtotal
        $baseAmount = $invoice->subtotal;
        $newFeeAmount = $setting->calculateFee($baseAmount);
        
        $fee->update([
            'invoice_total' => $baseAmount,
            'calculation_type' => $setting->calculation_type,
            'calculation_value' => $setting->value,
            'fee_amount' => $newFeeAmount,
            'notes' => 'Recalculado automáticamente - Subtotal: ' . number_format($baseAmount, 2),
        ]);
        
        return $fee;
    }
    
    /**
     * Obtener resumen de honorarios por audiólogo
     */
    public function getAudiologistSummary($audiologistId = null)
    {
        $query = AudiologistFee::query();
        
        if ($audiologistId) {
            $query->where('audiologist_id', $audiologistId);
        }
        
        return [
            'total_fees' => $query->sum('fee_amount'),
            'paid_fees' => (clone $query)->where('status', 'paid')->sum('fee_amount'),
            'pending_fees' => (clone $query)->where('status', 'pending')->sum('fee_amount'),
            'total_invoices' => $query->count(),
        ];
    }
    
    /**
     * Recalcular honorarios para todas las facturas pendientes de un audiólogo
     */
    public function recalculateAllFeesForAudiologist($audiologistId)
    {
        $invoices = Invoice::where('audiologist_id', $audiologistId)
            ->whereHas('audiologistFee', function($q) {
                $q->where('status', 'pending');
            })
            ->get();
        
        $updated = 0;
        foreach ($invoices as $invoice) {
            $this->recalculateFee($invoice);
            $updated++;
        }
        
        return $updated;
    }
}