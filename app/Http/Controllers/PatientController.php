<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Patient;
use App\Models\Insurance;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::with(['branch', 'insurance'])
            ->latest()
            ->paginate(10);

        return view('patients.index', compact('patients'));
    }

    /**
     * JSON — datos para modal de detalle (show)
     */
    public function showData(Patient $patient): JsonResponse
    {
        $patient->load(['branch', 'insurance']);

        return response()->json([
            'id'               => $patient->id,
            'first_name'       => $patient->first_name,
            'last_name'        => $patient->last_name,
            'cedula'           => $patient->cedula,
            'phone'            => $patient->phone,
            'email'            => $patient->email,
            'gender'           => $patient->gender,
            'birth_date'       => $patient->birth_date
                ? \Carbon\Carbon::parse($patient->birth_date)->format('d/m/Y')
                : null,
            'address'          => $patient->address,
            'branch'           => $patient->branch?->name,
            'insurance'        => $patient->insurance?->name,
            'insurance_number' => $patient->insurance_number,
            'created_at'       => $patient->created_at->format('d/m/Y H:i'),
        ]);
    }

    /**
     * JSON — datos para modal de edición
     */
    public function editData(Patient $patient): JsonResponse
    {
        return response()->json([
            'id'               => $patient->id,
            'first_name'       => $patient->first_name,
            'last_name'        => $patient->last_name,
            'cedula'           => $patient->cedula,
            'phone'            => $patient->phone,
            'email'            => $patient->email,
            'gender'           => $patient->gender,
            'birth_date_raw'   => $patient->birth_date
                ? \Carbon\Carbon::parse($patient->birth_date)->format('Y-m-d') // formato para input[type=date]
                : null,
            'address'          => $patient->address,
            'branch_id'        => $patient->branch_id,
            'insurance_id'     => $patient->insurance_id,
            'insurance_number' => $patient->insurance_number,
        ]);
    }

    public function create()
    {
        $branches   = Branch::orderBy('name')->get();
        $insurances = Insurance::where('active', 1)->orderBy('name')->get();
        return view('patients.create', compact('branches', 'insurances'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'       => 'required|string|max:255',
            'last_name'        => 'required|string|max:255',
            'cedula'           => 'required|string|max:255|unique:patients,cedula',
            'phone'            => 'nullable|string|max:50',
            'email'            => 'nullable|email|max:255',
            'birth_date'       => 'nullable|date',
            'gender'           => 'nullable|in:M,F',
            'address'          => 'nullable|string',
            'insurance_id'     => 'nullable|exists:insurances,id',
            'insurance_number' => 'nullable|string|max:100',
            'branch_id'        => 'nullable|exists:branches,id',
        ]);

        $data = $request->all();
        if (auth()->user()->role->name !== 'admin') {
            $data['branch_id'] = auth()->user()->branch_id;
        }

        $patient = Patient::create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Paciente registrado correctamente.',
                'patient' => $patient,
            ], 201);
        }

        return redirect()->route('patients.index')
            ->with('success', 'Paciente registrado correctamente.');
    }

    /**
     * AJAX store desde modal de facturación
     */
    public function storeAjax(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name'       => 'required|string|max:255',
            'last_name'        => 'required|string|max:255',
            'cedula'           => 'required|string|max:255|unique:patients,cedula',
            'phone'            => 'nullable|string|max:255',
            'email'            => 'nullable|email|max:255',
            'birth_date'       => 'nullable|date',
            'gender'           => 'nullable|in:M,F',
            'address'          => 'nullable|string',
            'insurance_id'     => 'nullable|exists:insurances,id',
            'insurance_number' => 'nullable|string|max:255',
            'branch_id'        => 'nullable|exists:branches,id',
        ]);

        $patient = Patient::create($validated);
        $patient->load('insurance');

        return response()->json([
            'success' => true,
            'patient' => [
                'id'                 => $patient->id,
                'full_name'          => $patient->first_name . ' ' . $patient->last_name,
                'cedula'             => $patient->cedula,
                'phone'              => $patient->phone,
                'insurance_id'       => $patient->insurance_id,
                'insurance_name'     => $patient->insurance?->name,
                'insurance_coverage' => $patient->insurance?->coverage_percentage,
            ],
            'message' => 'Paciente creado exitosamente.',
        ], 201);
    }

    public function show(Patient $patient)
    {
        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        $branches   = Branch::orderBy('name')->get();
        $insurances = Insurance::where('active', 1)->orderBy('name')->get();
        return view('patients.edit', compact('patient', 'branches', 'insurances'));
    }

    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'first_name'       => 'required|string|max:255',
            'last_name'        => 'required|string|max:255',
            'cedula'           => 'required|string|max:255|unique:patients,cedula,' . $patient->id,
            'phone'            => 'nullable|string|max:50',
            'email'            => 'nullable|email|max:255',
            'birth_date'       => 'nullable|date',
            'gender'           => 'nullable|in:M,F',
            'address'          => 'nullable|string',
            'insurance_id'     => 'nullable|exists:insurances,id',
            'insurance_number' => 'nullable|string|max:100',
            'branch_id'        => 'nullable|exists:branches,id',
        ]);

        $data = $request->all();
        if (auth()->user()->role->name !== 'admin') {
            $data['branch_id'] = auth()->user()->branch_id;
        }

        $patient->update($data);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Paciente actualizado correctamente.']);
        }

        return redirect()->route('patients.index')
            ->with('success', 'Paciente actualizado correctamente.');
    }

    public function destroy(Request $request, Patient $patient)
    {
        $patient->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Paciente eliminado correctamente.']);
        }

        return redirect()->route('patients.index')
            ->with('success', 'Paciente eliminado correctamente.');
    }

    
}