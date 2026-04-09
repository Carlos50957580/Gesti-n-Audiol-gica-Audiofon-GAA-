<?php

namespace App\Http\Controllers;

use App\Models\ClinicalRecord;
use App\Models\ClinicalRecordDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClinicalRecordDocumentController extends Controller
{
    public function index(ClinicalRecord $clinicalRecord)
    {
        $this->authorizeAudiologist($clinicalRecord);

        $docs = $clinicalRecord->documents()
            ->with('uploadedBy')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($d) => [
                'id'            => $d->id,
                'name'          => $d->name,
                'file_name'     => $d->file_name,
                'file_type'     => $d->file_type,
                'file_size'     => $d->file_size_formatted,
                'file_icon'     => $d->file_icon,
                'file_icon_color' => $d->file_icon_color,
                'uploaded_by'   => $d->uploadedBy->name ?? '—',
                'created_at'    => $d->created_at->format('d/m/Y H:i'),
                'download_url'  => route('clinical-records.documents.download', $d->id),
                'delete_url'    => route('clinical-records.documents.destroy', $d->id),
            ]);

        return response()->json(['documents' => $docs]);
    }

    public function store(Request $request, ClinicalRecord $clinicalRecord)
    {
        $this->authorizeAudiologist($clinicalRecord);

        $request->validate([
            'document'      => 'required|file|mimes:pdf,doc,docx|max:10240', // 10MB
            'document_name' => 'required|string|max:255',
        ]);

        $file      = $request->file('document');
        $extension = strtolower($file->getClientOriginalExtension());
        $fileName  = $file->getClientOriginalName();
        $slug      = Str::slug($request->document_name) . '_' . time() . '.' . $extension;
        $path      = $file->storeAs(
            'clinical-documents/' . $clinicalRecord->patient_id,
            $slug,
            'public'
        );

        $doc = ClinicalRecordDocument::create([
            'clinical_record_id' => $clinicalRecord->id,
            'patient_id'         => $clinicalRecord->patient_id,
            'name'               => $request->document_name,
            'file_path'          => $path,
            'file_name'          => $fileName,
            'file_type'          => $extension,
            'file_size'          => $file->getSize(),
            'uploaded_by'        => auth()->id(),
        ]);

        return response()->json([
            'message'  => 'Documento subido correctamente.',
            'document' => [
                'id'              => $doc->id,
                'name'            => $doc->name,
                'file_name'       => $doc->file_name,
                'file_type'       => $doc->file_type,
                'file_size'       => $doc->file_size_formatted,
                'file_icon'       => $doc->file_icon,
                'file_icon_color' => $doc->file_icon_color,
                'uploaded_by'     => auth()->user()->name,
                'created_at'      => $doc->created_at->format('d/m/Y H:i'),
                'download_url'    => route('clinical-records.documents.download', $doc->id),
                'delete_url'      => route('clinical-records.documents.destroy', $doc->id),
            ],
        ]);
    }

    public function download(ClinicalRecordDocument $document)
    {
        // Solo el audiólogo asignado puede descargar
        if ($document->clinicalRecord->audiologist_id !== auth()->id()) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Archivo no encontrado.');
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    public function destroy(ClinicalRecordDocument $document)
    {
        if ($document->clinicalRecord->audiologist_id !== auth()->id()) {
            abort(403);
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return response()->json(['message' => 'Documento eliminado correctamente.']);
    }

    // ── Helper ────────────────────────────────────────────
    private function authorizeAudiologist(ClinicalRecord $cr): void
    {
        if ($cr->audiologist_id !== auth()->id()) {
            abort(403);
        }
    }
}