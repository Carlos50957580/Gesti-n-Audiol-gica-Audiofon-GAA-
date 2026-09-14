<?php

namespace App\Http\Controllers;

use App\Models\NcfSequence;
use App\Models\NcfType;
use Illuminate\Http\Request;

class NcfLookupController extends Controller
{
    /**
     * Obtiene el próximo NCF disponible según el tipo y la sucursal
     */
    public function nextAvailable(Request $request)
    {
        $request->validate([
            'ncf_type'  => 'required|in:consumidor_final,credito_fiscal,gubernamental,regimen_especial',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        // Mapear el tipo de NCF del frontend (consumidor_final, etc.) al código de la tabla ncf_types (B02, B01, etc.)
        $typeCodeMap = [
            'consumidor_final' => 'B02',
            'credito_fiscal'   => 'B01',
            'gubernamental'    => 'B15',
            'regimen_especial' => 'B14',
        ];

        $typeCode = $typeCodeMap[$request->ncf_type];
        $ncfType = NcfType::where('code', $typeCode)->first();

        if (!$ncfType) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de NCF no configurado en el sistema.',
            ], 404);
        }

        // Buscar la secuencia activa:
        // 1. Primero intentar con la sucursal específica
        // 2. Si no hay, usar la general (branch_id = null)
        $sequence = NcfSequence::active()
            ->where('ncf_type_id', $ncfType->id)
            ->where(function ($q) use ($request) {
                if ($request->branch_id) {
                    $q->where('branch_id', $request->branch_id)
                      ->orWhereNull('branch_id');
                } else {
                    $q->whereNull('branch_id');
                }
            })
            ->where('valid_until', '>=', now()->toDateString())
            ->where('valid_from', '<=', now()->toDateString())
            ->whereRaw('CAST(current_number AS UNSIGNED) <= CAST(end_number AS UNSIGNED)')
            ->orderByRaw('CASE WHEN branch_id = ? THEN 0 ELSE 1 END', [$request->branch_id ?? 0])
            ->orderBy('valid_until')
            ->first();

        if (!$sequence) {
            return response()->json([
                'success' => false,
                'message' => 'No hay una secuencia NCF activa para este tipo de comprobante. Contacta al administrador.',
            ], 404);
        }

        if (!$sequence->canIssue()) {
            return response()->json([
                'success' => false,
                'message' => 'La secuencia NCF seleccionada no puede emitir más comprobantes (' . $sequence->status_label . ').',
            ], 400);
        }

        return response()->json([
            'success'      => true,
            'ncf'          => $sequence->peekNext(),
            'sequence_id'  => $sequence->id,
            'sequence_name' => $sequence->name,
            'remaining'    => $sequence->remaining,
            'is_low'       => $sequence->remaining <= $sequence->alert_threshold,
            'alert_threshold' => $sequence->alert_threshold,
        ]);
    }
}