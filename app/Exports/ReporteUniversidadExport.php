<?php

namespace App\Exports;

use App\Models\PlanEstudio;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;

class ReporteUniversidadExport implements FromCollection
{
    public function collection()
    {
        return PlanEstudio::where('university_id', Auth::user()->university_id)->get([
            'codigo_plan',
            'nombre_programa',
            'nivel_academico',
            'modalidad',
            'estado'
        ]);
    }
}
