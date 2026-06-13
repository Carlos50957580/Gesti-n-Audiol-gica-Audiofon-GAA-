<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Insurance;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Services\AudiologistFeeService;

class InvoiceController extends Controller
{


    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Verificar si es admin2 (role_id = 4)
        $isAdmin2 = $user->role_id == 4;

        // ── QUERY PRINCIPAL (LISTADO) ─────────────────────
        $query = Invoice::with(['patient', 'user', 'branch', 'insurance'])
            ->orderBy('created_at', 'desc');

        // 🔥 PARA ADMIN2: Solo mostrar facturas con seguro (insurance_id no es null)
        if ($isAdmin2) {
            $query->whereNotNull('insurance_id');
        }

        // 🔥 PARA ADMIN2: NO restringir por sucursal, puede ver todas
        // Restricción por sucursal solo para usuarios normales (no admin, no admin2)
        if ($user->role->name !== 'admin' && !$isAdmin2) {
            $query->where('branch_id', $user->branch_id);
        }

        // 🔍 BÚSQUEDA
        if ($request->filled('q')) {
            $q         = $request->q;
            $numericId = null;

            if (preg_match('/(?:FAC-?)?(\d+)/i', $q, $m)) {
                $numericId = (int) $m[1];
            }

            $query->where(function ($sq) use ($q, $numericId) {
                if ($numericId) {
                    $sq->orWhere('id', $numericId);
                }

                $sq->orWhereHas('patient', function ($pq) use ($q) {
                    $pq->where('first_name', 'like', "%{$q}%")
                       ->orWhere('last_name',  'like', "%{$q}%")
                       ->orWhere('cedula',     'like', "%{$q}%")
                       ->orWhereRaw("CONCAT(first_name,' ',last_name) LIKE ?", ["%{$q}%"]);
                });
            });
        }

        // 🎯 FILTROS
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 🔥 Admin y admin2 pueden filtrar por sucursal
        if (($user->role->name === 'admin' || $isAdmin2) && $request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // 📄 PAGINACIÓN
        $invoices = $query->paginate(15)->withQueryString();

        // ── STATS (SIN PAGINACIÓN) ─────────────────────
        $statsQuery = Invoice::query();

        // 🔥 PARA ADMIN2: Solo contar facturas con seguro
        if ($isAdmin2) {
            $statsQuery->whereNotNull('insurance_id');
        }

        // 🔥 PARA ADMIN2: NO restringir por sucursal en stats
        // Restricción por sucursal solo para usuarios normales
        if ($user->role->name !== 'admin' && !$isAdmin2) {
            $statsQuery->where('branch_id', $user->branch_id);
        }

        // Aplicar mismos filtros
        if ($request->filled('status')) {
            $statsQuery->where('status', $request->status);
        }

        if (($user->role->name === 'admin' || $isAdmin2) && $request->filled('branch_id')) {
            $statsQuery->where('branch_id', $request->branch_id);
        }

        if ($request->filled('date_from')) {
            $statsQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $statsQuery->whereDate('created_at', '<=', $request->date_to);
        }

        // ⚡ Cálculos
        $stats = [
            'total'       => (clone $statsQuery)->count(),
            'pending'     => (clone $statsQuery)->where('status', 'pendiente')->count(),
            'paid'        => (clone $statsQuery)->where('status', 'pagada')->count(),
            'cancelled'   => (clone $statsQuery)->where('status', 'cancelada')->count(),
            'total_amt'   => (clone $statsQuery)->sum('total'),
            'pending_amt' => (clone $statsQuery)->where('status', 'pendiente')->sum('total'),
            'paid_amt'    => (clone $statsQuery)->where('status', 'pagada')->sum('total'),
        ];

        // ── SUCURSALES (admin y admin2 pueden ver todas) ─────────────────────
        $branches = ($user->role->name === 'admin' || $isAdmin2)
            ? Branch::orderBy('name')->get()
            : collect();

        return view('invoices.index', compact('invoices', 'branches', 'stats', 'isAdmin2'));
    }


    public function create()
{
    $user = auth()->user();

    $services   = Service::where('active', 1)->orderBy('name')->get();
    $insurances = Insurance::where('active', 1)->orderBy('name')->get();
    $branches   = Branch::orderBy('name')->get();

    // ✅ CORREGIDO: Obtener audiólogos por role_id - SIN RESTRICCIÓN DE SUCURSAL
    $audiologistRole = \App\Models\Role::where('name', 'audiologist')->orWhere('name', 'audiologo')->first();
    $audiologists = collect();
    
    if ($audiologistRole) {
        // 🔥 ELIMINAR o COMENTAR la restricción de sucursal
        $query = \App\Models\User::where('role_id', $audiologistRole->id);
        
        // 🔥 COMENTAR ESTAS LÍNEAS PARA QUE LOS RECEPCIONISTAS VENA TODOS LOS AUDIÓLOGOS
        // if ($user->role->name !== 'admin') {
        //     $query->where('branch_id', $user->branch_id);
        // }
        
        $audiologists = $query->orderBy('name')->get();
    }

    return view('invoices.create', compact('services', 'insurances', 'branches', 'audiologists'));
}

    public function store(Request $request)
    {
        $request->validate([
            'patient_id'              => 'required|exists:patients,id',
            'branch_id'               => 'required|exists:branches,id',
            'audiologist_id'          => 'required|exists:users,id',
            'services'                => 'required|array|min:1',
            'services.*.id'           => 'required|exists:services,id',
            'services.*.quantity'     => 'required|integer|min:1',
            'services.*.custom_price' => 'nullable|numeric|min:0',
            'services.*.cov_value'    => 'nullable|numeric|min:0',
            'services.*.cov_type'     => 'nullable|in:pct,amt',
            'insurance_id'            => 'nullable|exists:insurances,id',
            'authorization_number'    => 'nullable|string|max:255',
            'with_ncf'               => 'nullable|boolean',
            'ncf'                    => 'nullable|string|max:20',
            'ncf_type'               => 'nullable|string|max:50',
            'customer_rnc'           => 'nullable|string|max:20',
            'customer_business_name' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $insurance         = $request->insurance_id ? Insurance::find($request->insurance_id) : null;
            $subtotal          = 0;
            $insuranceDiscount = 0;
            $items             = [];

            foreach ($request->services as $svc) {
                $service = Service::findOrFail($svc['id']);
                $qty     = (int) $svc['quantity'];

                // ── Precio personalizado o precio del catálogo ──────────
                $price = isset($svc['custom_price']) && (float) $svc['custom_price'] > 0
                    ? (float) $svc['custom_price']
                    : (float) $service->price;

                $lineSubtotal    = $price * $qty;
                $coveragePct     = null;
                $insuranceAmount = 0;
                $patientAmount   = $lineSubtotal;

                if ($insurance) {
                    $covValue = isset($svc['cov_value']) ? (float) $svc['cov_value'] : 0;
                    $covType  = $svc['cov_type'] ?? 'pct';

                    if ($covType === 'pct') {
                        $pct             = min($covValue, 100);
                        $insuranceAmount = round($lineSubtotal * ($pct / 100), 2);
                        $coveragePct     = $pct;
                    } else {
                        $insuranceAmount = min($covValue, $lineSubtotal);
                        $coveragePct     = $lineSubtotal > 0
                            ? round(($insuranceAmount / $lineSubtotal) * 100, 2)
                            : 0;
                    }

                    $patientAmount = $lineSubtotal - $insuranceAmount;
                }

                $subtotal          += $lineSubtotal;
                $insuranceDiscount += $insuranceAmount;

                $items[] = [
                    'service_id'          => $service->id,
                    'price'               => $price,
                    'quantity'            => $qty,
                    'subtotal'            => $lineSubtotal,
                    'coverage_percentage' => $coveragePct,
                    'insurance_amount'    => $insuranceAmount ?: null,
                    'patient_amount'      => $patientAmount,
                ];
            }

            $total = $subtotal - $insuranceDiscount;
            
            $invoice = Invoice::create([
                'patient_id'           => $request->patient_id,
                'user_id'              => Auth::id(),
                'audiologist_id'       => $request->audiologist_id,
                'branch_id'            => $request->branch_id,
                'insurance_id'         => $insurance?->id,
                'subtotal'             => $subtotal,
                'insurance_discount'   => $insuranceDiscount,
                'total'                => $total,
                'status'               => 'pendiente',
                'authorization_number' => $request->authorization_number,
                'with_ncf' => $request->boolean('with_ncf'),
                'ncf_type' => $request->ncf_type,
                'customer_rnc' => $request->customer_rnc,
                'customer_business_name' => $request->customer_business_name,
                'ncf' => $request->ncf,
            ]);

            foreach ($items as $item) {
                $item['invoice_id'] = $invoice->id;
                InvoiceItem::create($item);
            }

            // ═══════════════════════════════════════════════════════════
            // 👇 **AQUÍ SE AGREGA EL CÁLCULO DE HONORARIOS** 👇
            // ═══════════════════════════════════════════════════════════
            
            // Crear el servicio de honorarios y calcular automáticamente
            $feeService = new AudiologistFeeService();
            $fee = $feeService->calculateAndCreateFee($invoice);
            
            // Opcional: Log para verificar que se creó correctamente
            if ($fee) {
                \Illuminate\Support\Facades\Log::info('Honorario creado automáticamente', [
                    'invoice_id' => $invoice->id,
                    'audiologist_id' => $invoice->audiologist_id,
                    'fee_amount' => $fee->fee_amount
                ]);
            } else {
                \Illuminate\Support\Facades\Log::warning('No se pudo crear honorario', [
                    'invoice_id' => $invoice->id,
                    'audiologist_id' => $invoice->audiologist_id
                ]);
            }
            
            // ═══════════════════════════════════════════════════════════
            // 👆 **FIN DEL CÓDIGO AGREGADO** 👆
            // ═══════════════════════════════════════════════════════════

            DB::commit();

            return redirect()
                ->route('invoices.show', $invoice)
                ->with('success', 'Factura ' . $invoice->invoice_number . ' creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al crear la factura: ' . $e->getMessage());
        }
    }

    public function show(Invoice $invoice)
{
    $invoice->load([
        'patient',
        'user.role',
        'branch',
        'insurance',
        'items.service',
        'audiologist',
    ]);

    return view('invoices.show', compact('invoice'));
}

    public function cancel(Invoice $invoice)
    {
        if ($invoice->status !== 'pendiente') {
            return back()->with('error', 'Solo se pueden cancelar facturas pendientes.');
        }
        $invoice->update(['status' => 'cancelada']);
        return back()->with('success', 'Factura ' . $invoice->invoice_number . ' cancelada correctamente.');
    }

    public function searchPatients(Request $request)
    {
        $q = $request->get('q', '');

        $patients = Patient::with('insurance')
            ->where(function ($query) use ($q) {
                $query->where('first_name', 'like', "%{$q}%")
                      ->orWhere('last_name',  'like', "%{$q}%")
                      ->orWhere('cedula',     'like', "%{$q}%")
                      ->orWhereRaw("CONCAT(first_name,' ',last_name) LIKE ?", ["%{$q}%"]);
            })
            ->limit(10)
            ->get()
            ->map(fn($p) => [
                'id'                 => $p->id,
                'full_name'          => $p->first_name . ' ' . $p->last_name,
                'cedula'             => $p->cedula,
                'phone'              => $p->phone,
                'insurance_id'       => $p->insurance_id,
                'insurance_name'     => $p->insurance?->name,
                'insurance_coverage' => $p->insurance?->coverage_percentage,
            ]);

        return response()->json($patients);
    }

    public function getServicePrice(Service $service)
    {
        return response()->json(['id' => $service->id, 'name' => $service->name, 'price' => $service->price]);
    }

    public function consultRnc($rnc)
{
    try {

        $rnc = preg_replace('/[^0-9]/', '', $rnc);

        $response = Http::timeout(15)
            ->get('https://rnc.megaplus.com.do/api/consulta', [
                'rnc' => $rnc
            ]);

        return response()->json(
            $response->json(),
            $response->status()
        );

    } catch (\Exception $e) {

        return response()->json([
            'error' => true,
            'mensaje' => $e->getMessage()
        ], 500);
    }
}
}