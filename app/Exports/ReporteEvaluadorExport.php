<?php

namespace App\Exports;

use App\Models\Asignacion;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;

class ReporteEvaluadorExport implements FromCollection
{
    protected $filters;

    /**
     * 1. Constructor: Recibe los filtros pasados desde el controlador.
     */
    public function __construct(array $filters) 
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        // Consulta base: Asignaciones del evaluador actual.
        $query = Asignacion::where('evaluador_id', Auth::id());
        
        // 2. Aplicar el filtro de 'veredicto'
        // Verifica si el filtro existe Y si no es el valor vacío ('-- Todos --').
        if (isset($this->filters['veredicto']) && $this->filters['veredicto'] !== '') {
            $query->where('veredicto', $this->filters['veredicto']);
        }
        
        // Ejecuta la consulta y selecciona las columnas
        return $query->get([
            'veredicto',
            'observaciones',
            'plan_estudio_id',
            'updated_at'
        ]);
    }
}