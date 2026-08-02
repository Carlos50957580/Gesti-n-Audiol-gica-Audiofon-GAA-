<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\DoctorFee;
use App\Models\DoctorFeeSetting;
use Illuminate\Support\Facades\Log;

class InvoiceObserver
{
    /**
     * Handle the Invoice "updated" event.
     */
    public function updated(Invoice $invoice): void
    {
        // ✅ Generar Honorario Médico cuando la factura se marca como pagada
        if ($invoice->isDirty('status') && $invoice->status === 'pagada') {
            $this->generateDoctorFee($invoice);
        }
    }

    /**
     * Generar honorario para la factura
     */
    private function generateDoctorFee(Invoice $invoice): void
    {
        try {
            // Verificar que la factura tenga un médico asignado
            if (!$invoice->doctor_id) {
                Log::info('No se generó honorario para factura #' . $invoice->id . ' - No tiene médico asignado');
                return;
            }

            // Verificar que la factura tenga items
            if ($invoice->items->isEmpty()) {
                Log::info('No se generó honorario para factura #' . $invoice->id . ' - No tiene servicios');
                return;
            }

            // Verificar si ya existe un honorario para esta factura
            $existingFee = DoctorFee::where('invoice_id', $invoice->id)->first();
            if ($existingFee) {
                Log::info('Honorario ya existe para factura #' . $invoice->id);
                return;
            }

            $totalFee = 0;
            $settingsUsed = [];

            // Recorrer los items de la factura
            foreach ($invoice->items as $item) {
                $service = $item->service;
                if (!$service) continue;

                // Buscar configuración de honorarios para este médico y servicio
                $setting = DoctorFeeSetting::getForDoctorAndService(
                    $invoice->doctor_id,
                    $service->id
                );

                if ($setting) {
                    // Calcular el honorario para este servicio específico
                    $feeForService = $setting->calculateFee($item->subtotal);
                    $totalFee += $feeForService;
                    $settingsUsed[] = [
                        'service_id' => $service->id,
                        'service_name' => $service->name,
                        'subtotal' => $item->subtotal,
                        'fee_amount' => $feeForService,
                        'calculation_type' => $setting->calculation_type,
                        'calculation_value' => $setting->value,
                    ];
                }
            }

            // Si no hay configuración para ningún servicio, no generar honorario
            if (empty($settingsUsed) || $totalFee <= 0) {
                Log::info('No se generó honorario para factura #' . $invoice->id . ' - No hay configuración de honorarios');
                return;
            }

            // Determinar el tipo de cálculo principal
            $mainSetting = $settingsUsed[0];

            // Crear el honorario
            $fee = DoctorFee::create([
                'doctor_id' => $invoice->doctor_id,
                'invoice_id' => $invoice->id,
                'invoice_total' => $invoice->total,
                'calculation_type' => $mainSetting['calculation_type'] ?? 'percentage',
                'calculation_value' => $mainSetting['calculation_value'] ?? 0,
                'fee_amount' => $totalFee,
                'status' => 'pending',
                'notes' => 'Honorario generado automáticamente desde la factura #' . $invoice->id,
            ]);

            Log::info('✅ Honorario generado para factura #' . $invoice->id . ' - Médico: ' . ($invoice->doctor->name ?? 'N/A') . ' - Monto: RD$ ' . number_format($totalFee, 2));

        } catch (\Exception $e) {
            Log::error('❌ Error al generar honorario para factura #' . $invoice->id . ': ' . $e->getMessage());
        }
    }
}