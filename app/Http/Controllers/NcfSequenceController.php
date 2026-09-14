<?php

namespace App\Http\Controllers;

use App\Models\NcfSequence;
use App\Models\NcfType;
use App\Models\Branch;
use Illuminate\Http\Request;

class NcfSequenceController extends Controller
{
    /**
     * Listado de secuencias NCF
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->role->name === 'admin';

        $query = NcfSequence::with(['type', 'branch']);

        // Filtros
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('name', 'like', "%{$s}%");
        }

        if ($request->filled('type_id')) {
            $query->where('ncf_type_id', $request->type_id);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('status')) {
            match ($request->status) {
                'active'    => $query->where('is_active', true)->where('valid_until', '>=', now()),
                'inactive'  => $query->where('is_active', false),
                'expired'   => $query->where('valid_until', '<', now()),
                default     => null,
            };
        }

        $sequences = $query->orderByDesc('is_active')
            ->orderBy('valid_until')
            ->paginate(15);

        $types = NcfType::active()->orderBy('code')->get();
        $branches = Branch::where('is_active', 1)->orderBy('name')->get();

        // ── KPIs ────────────────────────────────────────────
        $totalSequences   = NcfSequence::count();
        $activeSequences  = NcfSequence::active()->where('valid_until', '>=', now())->count();
        $expiredSequences = NcfSequence::expired()->count();
        $lowStockSequences = NcfSequence::active()
            ->whereColumn('current_number', '>=', \DB::raw('CAST(end_number AS UNSIGNED) - alert_threshold'))
            ->count();

        return view('ncf-sequences.index', compact(
            'sequences', 'types', 'branches',
            'totalSequences', 'activeSequences', 'expiredSequences', 'lowStockSequences'
        ));
    }

    /**
     * Formulario de nueva secuencia
     */
    public function create()
    {
        $types = NcfType::active()->orderBy('code')->get();
        $branches = Branch::where('is_active', 1)->orderBy('name')->get();

        return view('ncf-sequences.create', compact('types', 'branches'));
    }

    /**
     * Guardar secuencia
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'ncf_type_id'     => 'required|exists:ncf_types,id',
            'branch_id'       => 'nullable|exists:branches,id',
            'prefix'          => 'required|string|max:10',
            'serie'           => 'required|string|max:3',
            'start_number'    => 'required|string|max:20',
            'end_number'      => 'required|string|max:20',
            'alert_threshold' => 'required|integer|min:1',
            'valid_from'      => 'required|date',
            'valid_until'     => 'required|date|after:valid_from',
            'is_active'       => 'boolean',
            'notes'           => 'nullable|string',
        ]);

        // Validar que start <= end
        if ((int) $data['start_number'] > (int) $data['end_number']) {
            return back()->withErrors([
                'start_number' => 'El número inicial no puede ser mayor al número final.'
            ])->withInput();
        }

        // El current_number inicia en start_number
        $data['current_number'] = str_pad($data['start_number'], 8, '0', STR_PAD_LEFT);
        $data['start_number']   = str_pad($data['start_number'], 8, '0', STR_PAD_LEFT);
        $data['end_number']     = str_pad($data['end_number'], 8, '0', STR_PAD_LEFT);

        NcfSequence::create($data);

        return redirect(url('/ncf-sequences'))
            ->with('success', 'Secuencia NCF creada exitosamente.');
    }

    /**
     * Ver detalle
     */
    public function show(NcfSequence $ncfSequence)
    {
        $ncfSequence->load(['type', 'branch']);

        return view('ncf-sequences.show', compact('ncfSequence'));
    }

    /**
     * Formulario de edición
     */
    public function edit(NcfSequence $ncfSequence)
    {
        $types = NcfType::active()->orderBy('code')->get();
        $branches = Branch::where('is_active', 1)->orderBy('name')->get();

        return view('ncf-sequences.edit', compact('ncfSequence', 'types', 'branches'));
    }

    /**
     * Actualizar secuencia
     */
    public function update(Request $request, NcfSequence $ncfSequence)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'ncf_type_id'     => 'required|exists:ncf_types,id',
            'branch_id'       => 'nullable|exists:branches,id',
            'prefix'          => 'required|string|max:10',
            'serie'           => 'required|string|max:3',
            'end_number'      => 'required|string|max:20',
            'alert_threshold' => 'required|integer|min:1',
            'valid_from'      => 'required|date',
            'valid_until'     => 'required|date|after:valid_from',
            'is_active'       => 'boolean',
            'notes'           => 'nullable|string',
        ]);

        // Solo permitir editar el end_number si es mayor al current
        $endNumber = (int) $data['end_number'];
        if ($endNumber < (int) $ncfSequence->current_number) {
            return back()->withErrors([
                'end_number' => 'El número final no puede ser menor al número actual (' . $ncfSequence->current_ncf . ').'
            ])->withInput();
        }

        $data['end_number'] = str_pad($data['end_number'], 8, '0', STR_PAD_LEFT);

        $ncfSequence->update($data);

        return redirect(url('/ncf-sequences'))
            ->with('success', 'Secuencia NCF actualizada exitosamente.');
    }

    /**
     * Eliminar secuencia
     */
    public function destroy(NcfSequence $ncfSequence)
    {
        // No permitir eliminar si ya se usó (más de 0 NCF emitidos)
        if ($ncfSequence->used > 0) {
            return back()->with('error', 'No se puede eliminar una secuencia que ya ha emitido NCF.');
        }

        $ncfSequence->delete();

        return redirect(url('/ncf-sequences'))
            ->with('success', 'Secuencia NCF eliminada exitosamente.');
    }

    /**
     * Activar/Desactivar secuencia
     */
    public function toggle(NcfSequence $ncfSequence)
    {
        $ncfSequence->update(['is_active' => !$ncfSequence->is_active]);

        $status = $ncfSequence->is_active ? 'activada' : 'desactivada';

        return back()->with('success', "Secuencia {$status} exitosamente.");
    }

    /**
     * API: Obtener la siguiente secuencia activa por tipo y sucursal
     */
    public function getNextAvailable(Request $request)
    {
        $request->validate([
            'ncf_type_id' => 'required|exists:ncf_types,id',
            'branch_id'   => 'nullable|exists:branches,id',
        ]);

        $sequence = NcfSequence::active()
            ->where('ncf_type_id', $request->ncf_type_id)
            ->when($request->branch_id, fn($q) => $q->where('branch_id', $request->branch_id))
            ->where('valid_until', '>=', now())
            ->whereColumn('current_number', '<=', 'end_number')
            ->orderBy('valid_until')
            ->first();

        if (!$sequence) {
            return response()->json([
                'success' => false,
                'message' => 'No hay secuencias disponibles para este tipo de comprobante.',
            ], 404);
        }

        return response()->json([
            'success'    => true,
            'ncf'        => $sequence->peekNext(),
            'sequence_id' => $sequence->id,
            'remaining'  => $sequence->remaining,
        ]);
    }
}