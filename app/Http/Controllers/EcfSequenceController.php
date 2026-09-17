<?php

namespace App\Http\Controllers;

use App\Models\EcfSequence;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EcfSequenceController extends Controller
{
   

    /**
     * Listado de secuencias
     */
    public function index(Request $request)
    {
        $query = EcfSequence::with('branch');

        // Filtros
        if ($request->filled('tipo_ecf')) {
            $query->where('tipo_ecf', $request->tipo_ecf);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado === '1');
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('prefijo', 'like', "%{$search}%")
                  ->orWhere('notas', 'like', "%{$search}%");
            });
        }

        $secuencias = $query->orderBy('tipo_ecf')
                            ->orderBy('created_at', 'desc')
                            ->paginate(15)
                            ->withQueryString();

        $branches = Branch::orderBy('name')->get();
        $tiposEcf = EcfSequence::tiposEcf();

        // Estadísticas generales
        $stats = [
            'total' => EcfSequence::count(),
            'activas' => EcfSequence::where('estado', true)->count(),
            'agotadas' => EcfSequence::whereColumn('secuencia_actual', '>=', 'hasta')->count(),
            'por_vencer' => EcfSequence::where('estado', true)
                ->whereNotNull('fecha_vencimiento')
                ->where('fecha_vencimiento', '<=', now()->addDays(30))
                ->where('fecha_vencimiento', '>=', now())
                ->count(),
        ];

        return view('ecf-sequences.index', compact('secuencias', 'branches', 'tiposEcf', 'stats'));
    }

    /**
     * Formulario de creación
     */
    public function create()
    {
        $branches = Branch::orderBy('name')->get();
        $tiposEcf = EcfSequence::tiposEcf();

        return view('ecf-sequences.create', compact('branches', 'tiposEcf'));
    }

    /**
     * Guardar nueva secuencia
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipo_ecf' => 'required|string|max:2|in:31,32,33,34,41,43,44,45,46,47',
            'prefijo' => 'required|string|max:5',
            'desde' => 'required|integer|min:1',
            'hasta' => 'required|integer|min:1|gte:desde',
            'secuencia_actual' => 'nullable|integer|min:0',
            'fecha_vencimiento' => 'required_unless:tipo_ecf,32|nullable|date',
            'valid_from' => 'nullable|date',
            'branch_id' => 'nullable|exists:branches,id',
            'notas' => 'nullable|string|max:500',
            'estado' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Validación de continuidad (regla de DGII/EF2)
        $ultimaSecuencia = EcfSequence::where('tipo_ecf', $request->tipo_ecf)
            ->orderBy('hasta', 'desc')
            ->first();

        if ($ultimaSecuencia && $request->desde != ($ultimaSecuencia->hasta + 1)) {
            return redirect()->back()
                ->withErrors(['desde' => "El campo DESDE debe ser " . ($ultimaSecuencia->hasta + 1) . " para continuar la secuencia del tipo E{$request->tipo_ecf}."])
                ->withInput();
        }

        // Validar solapamiento
        $solapa = EcfSequence::where('tipo_ecf', $request->tipo_ecf)
            ->where(function ($q) use ($request) {
                $q->whereBetween('desde', [$request->desde, $request->hasta])
                  ->orWhereBetween('hasta', [$request->desde, $request->hasta])
                  ->orWhere(function ($q2) use ($request) {
                      $q2->where('desde', '<=', $request->desde)
                         ->where('hasta', '>=', $request->hasta);
                  });
            })
            ->exists();

        if ($solapa) {
            return redirect()->back()
                ->withErrors(['desde' => 'El rango se solapa con otra secuencia existente del mismo tipo.'])
                ->withInput();
        }

        // Para E32 no requiere fecha de vencimiento
        $fechaVencimiento = $request->tipo_ecf === '32'
            ? now()->addYears(100)->toDateString()
            : $request->fecha_vencimiento;

        EcfSequence::create([
            'tipo_ecf' => $request->tipo_ecf,
            'prefijo' => $request->prefijo,
            'desde' => $request->desde,
            'hasta' => $request->hasta,
            'secuencia_actual' => $request->secuencia_actual ?? 0,
            'fecha_vencimiento' => $fechaVencimiento,
            'valid_from' => $request->valid_from ?? now()->toDateString(),
            'estado' => $request->boolean('estado', true),
            'branch_id' => $request->branch_id,
            'notas' => $request->notas,
        ]);

        return redirect()->route('ecf-sequences.index')
            ->with('success', 'Secuencia e-CF creada correctamente.');
    }

    /**
     * Ver detalle
     */
    public function show(EcfSequence $ecfSequence)
    {
        $ecfSequence->load('branch');

        // Contar facturas emitidas con esta secuencia
        $facturasProductos = $ecfSequence->productInvoices()->count();
        $facturasServicios = $ecfSequence->invoices()->count();

        return view('ecf-sequences.show', compact('ecfSequence', 'facturasProductos', 'facturasServicios'));
    }

    /**
     * Formulario de edición
     */
    public function edit(EcfSequence $ecfSequence)
    {
        $branches = Branch::orderBy('name')->get();
        $tiposEcf = EcfSequence::tiposEcf();

        return view('ecf-sequences.edit', compact('ecfSequence', 'branches', 'tiposEcf'));
    }

    /**
     * Actualizar secuencia
     */
    public function update(Request $request, EcfSequence $ecfSequence)
    {
        $validator = Validator::make($request->all(), [
            'prefijo' => 'required|string|max:5',
            'desde' => 'required|integer|min:1',
            'hasta' => 'required|integer|min:1|gte:desde',
            'secuencia_actual' => 'nullable|integer|min:0',
            'fecha_vencimiento' => 'nullable|date',
            'valid_from' => 'nullable|date',
            'branch_id' => 'nullable|exists:branches,id',
            'notas' => 'nullable|string|max:500',
            'estado' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // No permitir editar si ya tiene facturas asociadas
        if ($ecfSequence->secuencia_actual > $ecfSequence->desde - 1) {
            $request->merge(['desde' => $ecfSequence->desde]);
        }

        $ecfSequence->update([
            'prefijo' => $request->prefijo,
            'desde' => $ecfSequence->desde,
            'hasta' => $request->hasta,
            'secuencia_actual' => $request->secuencia_actual ?? $ecfSequence->secuencia_actual,
            'fecha_vencimiento' => $request->tipo_ecf === '32'
                ? $ecfSequence->fecha_vencimiento
                : $request->fecha_vencimiento,
            'valid_from' => $request->valid_from,
            'estado' => $request->boolean('estado'),
            'branch_id' => $request->branch_id,
            'notas' => $request->notas,
        ]);

        return redirect()->route('ecf-sequences.index')
            ->with('success', 'Secuencia e-CF actualizada correctamente.');
    }

    /**
     * Eliminar secuencia (solo si no tiene facturas)
     */
    public function destroy(EcfSequence $ecfSequence)
    {
        // No se puede eliminar si tiene facturas asociadas
        $tieneFacturas = $ecfSequence->productInvoices()->exists()
                      || $ecfSequence->invoices()->exists();

        if ($tieneFacturas) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar una secuencia que ya tiene facturas asociadas. Puedes desactivarla.');
        }

        // Solo se pueden eliminar si están inactivas (regla EF2)
        if ($ecfSequence->estado) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar una secuencia activa. Desactívala primero.');
        }

        $ecfSequence->delete();

        return redirect()->route('ecf-sequences.index')
            ->with('success', 'Secuencia e-CF eliminada correctamente.');
    }

    /**
     * Activar/desactivar secuencia (toggle rápido)
     */
    public function toggle(EcfSequence $ecfSequence)
    {
        // Si está intentando activar, validar que no exista otra activa del mismo tipo
        if (!$ecfSequence->estado) {
            $otraActiva = EcfSequence::where('tipo_ecf', $ecfSequence->tipo_ecf)
                ->where('estado', true)
                ->where('id', '!=', $ecfSequence->id)
                ->exists();

            if ($otraActiva) {
                return redirect()->back()
                    ->with('error', "Ya existe otra secuencia activa del tipo E{$ecfSequence->tipo_ecf}. Desactívala primero.");
            }
        }

        $ecfSequence->update(['estado' => !$ecfSequence->estado]);

        $mensaje = $ecfSequence->estado ? 'activada' : 'desactivada';

        return redirect()->back()->with('success', "Secuencia {$mensaje} correctamente.");
    }
}