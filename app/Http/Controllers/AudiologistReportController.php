<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClinicalRecord;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AudiologistReportController extends Controller
{
    public function index()
    {
        return view('reports.audiologist');
    }

    private function getRange(Request $request): array
    {
        $dateFrom = $request->filled('date_from') ? $request->date_from : now()->startOfMonth()->toDateString();
        $dateTo   = $request->filled('date_to')   ? $request->date_to   : now()->toDateString();

        return [
            'from'      => $dateFrom . ' 00:00:00',
            'to'        => $dateTo   . ' 23:59:59',
            'date_from' => $dateFrom,
            'date_to'   => $dateTo,
        ];
    }

    // ── Citas ────────────────────────────────────────────────
    public function appointments(Request $request): JsonResponse
{
    $userId = auth()->id();
    $range  = $this->getRange($request);

    $base = fn() => Appointment::where('audiologist_id', $userId)
        ->whereBetween('appointment_date', [$range['date_from'], $range['date_to']]);

    $total       = $base()->count();
    $completadas = $base()->where('status', 'completada')->count();
    $programadas = $base()->where('status', 'programada')->count();
    $canceladas  = $base()->where('status', 'cancelada')->count();

    // Por mes
    $byMonth = $base()
        ->selectRaw("DATE_FORMAT(appointment_date, '%Y-%m') as month, COUNT(*) as count")
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    // Por día de la semana
    $byWeekdayRaw = $base()
        ->selectRaw("DAYOFWEEK(appointment_date) as dow, COUNT(*) as count")
        ->groupBy('dow')
        ->orderBy('dow')
        ->get()
        ->keyBy('dow');

    $days      = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
    $byWeekday = [];
    for ($i = 1; $i <= 7; $i++) {
        $byWeekday[] = ['day' => $days[$i-1], 'count' => $byWeekdayRaw[$i]->count ?? 0];
    }

    // Por sucursal
    $byBranch = DB::table('appointments')
        ->join('branches', 'appointments.branch_id', '=', 'branches.id')
        ->where('appointments.audiologist_id', $userId)
        ->whereBetween('appointments.appointment_date', [$range['date_from'], $range['date_to']])
        ->selectRaw('branches.name as branch, COUNT(*) as count,
            SUM(CASE WHEN appointments.status = "completada" THEN 1 ELSE 0 END) as completadas')
        ->groupBy('branches.id', 'branches.name')
        ->orderByDesc('count')
        ->get();

    // ── Próximas citas — estado "programada" ──────────────
    $upcoming = Appointment::with(['patient', 'branch'])
        ->where('audiologist_id', $userId)
        ->where('status', 'programada')
        ->whereBetween('appointment_date', [
            now()->toDateString(),
            now()->addDays(7)->toDateString(),
        ])
        ->orderBy('appointment_date')
        ->orderBy('appointment_time')
        ->limit(10)
        ->get()
        ->map(fn($a) => [
            'patient' => $a->patient->first_name . ' ' . $a->patient->last_name,
            'date'    => \Carbon\Carbon::parse($a->appointment_date)->format('d/m/Y'),
            'time'    => substr($a->appointment_time, 0, 5),
            'status'  => $a->status,
            'branch'  => $a->branch->name,
            'notes'   => $a->notes,
        ]);

    return response()->json([
        'kpis' => [
            'total'       => $total,
            'completadas' => $completadas,
            'programadas' => $programadas,
            'canceladas'  => $canceladas,
            'tasa'        => $total > 0 ? round(($completadas / $total) * 100, 1) : 0,
        ],
        'by_month'   => $byMonth,
        'by_weekday' => $byWeekday,
        'by_branch'  => $byBranch,
        'upcoming'   => $upcoming,
    ]);
}

    // ── Historias Clínicas ───────────────────────────────────
    public function clinicalRecords(Request $request): JsonResponse
    {
        $userId = auth()->id();
        $range  = $this->getRange($request);

        // ── KPIs ────────────────────────────────────────────
        $total = DB::table('clinical_records')
            ->where('audiologist_id', $userId)
            ->whereBetween('created_at', [$range['from'], $range['to']])
            ->count();

        $completadas = DB::table('clinical_records')
            ->where('audiologist_id', $userId)
            ->where('status', 'completada')
            ->whereBetween('created_at', [$range['from'], $range['to']])
            ->count();

        $pendientes = DB::table('clinical_records')
            ->where('audiologist_id', $userId)
            ->where('status', 'pendiente')
            ->whereBetween('created_at', [$range['from'], $range['to']])
            ->count();

        // ── Por mes ──────────────────────────────────────────
        $byMonth = DB::table('clinical_records')
            ->where('audiologist_id', $userId)
            ->where('status', 'completada')
            ->whereBetween('created_at', [$range['from'], $range['to']])
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // ── Por sucursal ─────────────────────────────────────
        $byBranch = DB::table('clinical_records')
            ->join('branches', 'clinical_records.branch_id', '=', 'branches.id')
            ->where('clinical_records.audiologist_id', $userId)
            ->whereBetween('clinical_records.created_at', [$range['from'], $range['to']])
            ->selectRaw('
                branches.name as branch,
                COUNT(*) as total,
                SUM(CASE WHEN clinical_records.status = "completada" THEN 1 ELSE 0 END) as completadas,
                SUM(CASE WHEN clinical_records.status = "pendiente"  THEN 1 ELSE 0 END) as pendientes
            ')
            ->groupBy('branches.id', 'branches.name')
            ->orderByDesc('total')
            ->get();

        // ── HCs pendientes (global, no filtrado por fecha) ───
        // Se muestran TODAS las pendientes sin importar el filtro de fecha
        // para que el audiólogo siempre vea lo que tiene que completar
        $pendingList = DB::table('clinical_records')
            ->join('patients', 'clinical_records.patient_id', '=', 'patients.id')
            ->join('invoices', 'clinical_records.invoice_id', '=', 'invoices.id')
            ->leftJoin('branches', 'clinical_records.branch_id', '=', 'branches.id')
            ->where('clinical_records.audiologist_id', $userId)
            ->where('clinical_records.status', 'pendiente')
            ->orderBy('clinical_records.created_at')
            ->select([
                DB::raw("CONCAT(patients.first_name,' ',patients.last_name) as patient"),
                'patients.cedula',
                'clinical_records.invoice_id',
                'clinical_records.created_at',
                DB::raw("COALESCE(branches.name, '—') as branch"),
            ])
            ->limit(15)
            ->get()
            ->map(fn($r) => [
                'patient'        => $r->patient,
                'cedula'         => $r->cedula,
                'invoice_number' => 'FAC-' . str_pad($r->invoice_id, 6, '0', STR_PAD_LEFT),
                'branch'         => $r->branch,
                'created_at'     => \Carbon\Carbon::parse($r->created_at)->format('d/m/Y'),
                'days_pending'   => \Carbon\Carbon::parse($r->created_at)->diffInDays(now()),
                'edit_url'       => route('clinical-records.edit', $r->invoice_id),
            ]);

        return response()->json([
            'kpis' => [
                'total'       => $total,
                'completadas' => $completadas,
                'pendientes'  => $pendientes,
                'tasa'        => $total > 0 ? round(($completadas / $total) * 100, 1) : 0,
            ],
            'by_month'    => $byMonth,
            'by_branch'   => $byBranch,
            'pending_list'=> $pendingList,
        ]);
    }
}