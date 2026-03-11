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

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['patient', 'user', 'branch', 'insurance'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $services   = Service::where('active', 1)->orderBy('name')->get();
        $insurances = Insurance::where('active', 1)->orderBy('name')->get();
        $branches   = Branch::orderBy('name')->get();

        return view('invoices.create', compact('services', 'insurances', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id'                  => 'required|exists:patients,id',
            'branch_id'                   => 'required|exists:branches,id',
            'services'                    => 'required|array|min:1',
            'services.*.id'               => 'required|exists:services,id',
            'services.*.quantity'         => 'required|integer|min:1',
            // cobertura por fila (opcional — el JS la envía pero no es obligatoria)
            'services.*.cov_value'        => 'nullable|numeric|min:0',
            'services.*.cov_type'         => 'nullable|in:pct,amt',
            'insurance_id'                => 'nullable|exists:insurances,id',
            'authorization_number'        => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $insurance = $request->insurance_id
                ? Insurance::find($request->insurance_id)
                : null;

            $subtotal          = 0;
            $insuranceDiscount = 0;
            $items             = [];

            foreach ($request->services as $svc) {
                $service      = Service::findOrFail($svc['id']);
                $qty          = (int) $svc['quantity'];
                $lineSubtotal = $service->price * $qty;

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
                        // Monto fijo — no puede superar el subtotal de la línea
                        $insuranceAmount = min($covValue, $lineSubtotal);
                        // Guardamos el porcentaje equivalente para registro
                        $coveragePct = $lineSubtotal > 0
                            ? round(($insuranceAmount / $lineSubtotal) * 100, 2)
                            : 0;
                    }

                    $patientAmount = $lineSubtotal - $insuranceAmount;
                }

                $subtotal          += $lineSubtotal;
                $insuranceDiscount += $insuranceAmount;

                $items[] = [
                    'service_id'          => $service->id,
                    'price'               => $service->price,
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
                'branch_id'            => $request->branch_id,
                'insurance_id'         => $insurance?->id,
                'subtotal'             => $subtotal,
                'insurance_discount'   => $insuranceDiscount,
                'total'                => $total,
                'status'               => 'pendiente',
                'authorization_number' => $request->authorization_number,
            ]);

            foreach ($items as $item) {
                $item['invoice_id'] = $invoice->id;
                InvoiceItem::create($item);
            }

            DB::commit();

            return redirect()
                ->route('invoices.show', $invoice)
                ->with('success', 'Factura ' . $invoice->invoice_number . ' creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al crear la factura: ' . $e->getMessage());
        }
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['patient', 'user', 'branch', 'insurance', 'items.service']);
        return view('invoices.show', compact('invoice'));
    }

    public function cancel(Invoice $invoice)
    {
        if ($invoice->status !== 'pendiente') {
            return back()->with('error', 'Solo se pueden cancelar facturas pendientes.');
        }
        $invoice->update(['status' => 'cancelada']);
        return back()->with('success', 'Factura cancelada correctamente.');
    }

    // ── AJAX ─────────────────────────────────────────────────────────────────

    public function searchPatients(Request $request)
    {
        $query = $request->get('q', '');

        $patients = Patient::with('insurance')
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name',  'like', "%{$query}%")
                  ->orWhere('cedula',     'like', "%{$query}%")
                  ->orWhereRaw("CONCAT(first_name,' ',last_name) LIKE ?", ["%{$query}%"]);
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
        return response()->json([
            'id'    => $service->id,
            'name'  => $service->name,
            'price' => $service->price,
        ]);
    }
}