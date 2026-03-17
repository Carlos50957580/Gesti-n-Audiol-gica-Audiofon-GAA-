<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\ClinicalRecord;

class InvoiceObserver
{
   public function updated(Invoice $invoice): void
{
    if ($invoice->isDirty('status') && $invoice->status === 'pagada') {
        ClinicalRecord::firstOrCreate(
            ['invoice_id' => $invoice->id],
            [
                'patient_id'     => $invoice->patient_id,
                'audiologist_id' => $invoice->audiologist_id,
                'branch_id'      => $invoice->branch_id,
                'status'         => 'pendiente',
            ]
        );
    }
}
}