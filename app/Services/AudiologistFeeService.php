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
            return null;
        }
        
        // Buscar la configuración de honorarios del audiólogo
        $setting = AudiologistFeeSetting::where('audiologist_id', $invoice->audiologist_id)
            ->where('is_active', true)
            ->first();
        
        if (!$setting) {
            return null;
        }
        
        // Verificar si ya existe un honorario para esta factura
        $existingFee = AudiologistFee::where('invoice_id', $invoice->id)->first();
        if ($existingFee) {
            return $existingFee;
        }
        
        // Calcular el honorario
        $feeAmount = $setting->calculateFee($invoice->total);
        
        // Crear el registro de honorario
        $fee = AudiologistFee::create([
            'audiologist_id' => $invoice->audiologist_id,
            'invoice_id' => $invoice->id,
            'invoice_total' => $invoice->total,
            'calculation_type' => $setting->calculation_type,
            'calculation_value' => $setting->value,
            'fee_amount' => $feeAmount,
            'status' => 'pending',
            'notes' => 'Generado automáticamente desde la factura #' . $invoice->id,
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
}