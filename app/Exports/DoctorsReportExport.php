<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Http\Request;

class DoctorsReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithEvents,
    WithCustomStartCell
{
    protected $request;
    protected $rowNumber = 0;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Invoice::query();

        // Mismos filtros que el controlador
        if ($this->request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $this->request->date_from);
        }
        if ($this->request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $this->request->date_to);
        }
        if ($this->request->filled('doctor_id')) {
            $query->where('doctor_id', $this->request->doctor_id);
        }
        if ($this->request->filled('branch_id')) {
            $query->where('branch_id', $this->request->branch_id);
        }
        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }
        if ($this->request->filled('insurance_id')) {
            if ($this->request->insurance_id === 'null') {
                $query->whereNull('insurance_id');
            } else {
                $query->where('insurance_id', $this->request->insurance_id);
            }
        }
        if ($this->request->filled('service_id') || $this->request->filled('category_id')) {
            $serviceId  = $this->request->service_id;
            $categoryId = $this->request->category_id;

            $query->whereHas('items', function ($q) use ($serviceId, $categoryId) {
                if ($serviceId) {
                    $q->where('service_id', $serviceId);
                }
                if ($categoryId) {
                    $q->whereHas('service', fn($sq) => $sq->where('category_id', $categoryId));
                }
            });
        }
        if ($this->request->filled('search')) {
            $search  = trim($this->request->search);
            $numeric = ltrim(preg_replace('/\D/', '', $search), '0');

            $query->where(function ($q) use ($search, $numeric) {
                $q->whereHas('patient', function ($pq) use ($search) {
                    $pq->where('first_name', 'LIKE', "%{$search}%")
                       ->orWhere('last_name', 'LIKE', "%{$search}%")
                       ->orWhere('cedula', 'LIKE', "%{$search}%")
                       ->orWhereRaw("CONCAT(first_name,' ',last_name) LIKE ?", ["%{$search}%"]);
                })
                ->orWhere('ncf', 'LIKE', "%{$search}%")
                ->orWhere('encf', 'LIKE', "%{$search}%");

                if ($numeric !== '') {
                    $q->orWhere('id', $numeric);
                }
            });
        }

        return $query->with([
            'patient', 'doctor', 'user', 'branch', 'insurance',
            'items.service.category', 'receipt.user', 'doctorFees',
        ])->orderByDesc('created_at')->get();
    }

    public function startCell(): string
    {
        return 'A3';
    }

    public function headings(): array
{
    // Sin "N° Factura" y sin las últimas 9 columnas
    return [
        'Fecha',
        'Estado',
        'Médico',
        'Sucursal',
        'Paciente',
        'Cédula',
        'Teléfono',
        'Seguro',
        'N° Autorización',
        'Estudios / Servicios',
        'Cant.',
        'Subtotal',
        'Impuestos',
        'Total c/ impuesto',
        'Desc. seguro',
        'Total',
        'Recibo',
        'Fecha pago',
        'Métodos de pago',
        'Efectivo',
        'Tarjeta',
        'Transferencia',
        'Ref. tarjeta',
        'Ref. transferencia',
    ];
}

public function map($invoice): array
{
    $this->rowNumber++;

    $rec = $invoice->receipt;

    $servicesText = $invoice->items->map(function ($item) {
        $cat = $item->service?->category?->name;
        return '• ' . ($item->service->name ?? 'Servicio eliminado')
            . ($cat ? " [{$cat}]" : '')
            . " ×{$item->quantity}"
            . ' (RD$ ' . number_format($item->subtotal, 2) . ')';
    })->implode("\n");

    return [
        $invoice->created_at->format('d/m/Y H:i'),
        ucfirst($invoice->status),
        $invoice->doctor->name ?? 'Sin médico',
        $invoice->branch->name ?? '—',
        $invoice->patient->full_name ?? 'N/A',
        $invoice->patient->cedula ?? '—',
        $invoice->patient->phone ?? '—',
        $invoice->insurance->name ?? 'Sin seguro',
        $invoice->authorization_number ?: '—',
        $servicesText ?: '—',
        $invoice->items->sum('quantity'),
        (float) $invoice->subtotal,
        (float) $invoice->tax_amount,
        (float) $invoice->total_with_tax,
        (float) $invoice->insurance_discount,
        (float) $invoice->total,
        $rec?->receipt_number ?? '—',
        $rec ? $rec->created_at->format('d/m/Y H:i') : '—',
        $rec?->payment_summary ?? '—',
        $rec && $rec->cash_amount ? (float) $rec->cash_amount : 0,
        $rec && $rec->card_amount ? (float) $rec->card_amount : 0,
        $rec && $rec->transfer_amount ? (float) $rec->transfer_amount : 0,
        $rec?->card_reference ?: '—',
        $rec?->transfer_reference ?: '—',
    ];
}

public function styles(Worksheet $sheet)
{
    // Columnas de dinero: L Subtotal, M Impuestos, N Total c/imp, O Desc.seguro, P Total, T Efectivo, U Tarjeta, V Transferencia
    $moneyColumns = ['L', 'M', 'N', 'O', 'P', 'T', 'U', 'V'];
    foreach ($moneyColumns as $col) {
        $sheet->getStyle("{$col}4:{$col}" . ($this->rowNumber + 3))
              ->getNumberFormat()
              ->setFormatCode('#,##0.00');
    }

    $sheet->getStyle('A3:X3')->getFont()->setBold(true)->setSize(10);
    $sheet->getStyle('A3:X3')->getAlignment()
          ->setHorizontal(Alignment::HORIZONTAL_CENTER)
          ->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->getRowDimension(3)->setRowHeight(35);

    return [];
}

public function registerEvents(): array
{
    return [
        AfterSheet::class => function (AfterSheet $event) {
            $sheet    = $event->sheet->getDelegate();
            $lastRow  = $this->rowNumber + 3;
            $lastCol  = 'X';
            $totalRow = $lastRow + 1;

            // ===== Título =====
            $sheet->mergeCells("A1:{$lastCol}1");
            $sheet->setCellValue('A1', 'REPORTE DE FACTURACIÓN POR MÉDICO');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setRGB('1F2937');
            $sheet->getStyle('A1')->getAlignment()
                  ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                  ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getRowDimension(1)->setRowHeight(28);

            $sheet->mergeCells("A2:{$lastCol}2");
            $sheet->setCellValue('A2', 'Generado el ' . now()->format('d/m/Y H:i'));
            $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(9)->getColor()->setRGB('6B7280');
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // ===== Encabezado =====
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '405189'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                    'wrapText'   => true,
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '2F3E6E']],
                ],
            ];
            $sheet->getStyle("A3:{$lastCol}3")->applyFromArray($headerStyle);

            // ===== Cuerpo =====
            $bodyRange = "A4:{$lastCol}{$lastRow}";
            $sheet->getStyle($bodyRange)->getAlignment()
                  ->setVertical(Alignment::VERTICAL_TOP)
                  ->setWrapText(true);
            $sheet->getStyle($bodyRange)->getBorders()->getAllBorders()
                  ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E5E7EB');
            $sheet->getStyle($bodyRange)->getFont()->setSize(9);

            for ($i = 4; $i <= $lastRow; $i++) {
                if ($i % 2 === 0) {
                    $sheet->getStyle("A{$i}:{$lastCol}{$i}")->getFill()
                          ->setFillType(Fill::FILL_SOLID)
                          ->getStartColor()->setRGB('F8FAFC');
                }
            }

            // ===== Fila de totales =====
            $sheet->getStyle("A{$totalRow}:{$lastCol}{$totalRow}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0AB39C'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Título del total (merge A:J)
            $sheet->mergeCells("A{$totalRow}:J{$totalRow}");
            $sheet->setCellValue("A{$totalRow}", 'TOTALES');

            // Cantidad (K)
            $sheet->setCellValue(
                "K{$totalRow}",
                "=SUMIF(\$B\$4:\$B\${$lastRow},\"<>Cancelada\",K4:K{$lastRow})"
            );

            // Columnas de dinero
            $moneyCols = ['L', 'M', 'N', 'O', 'P', 'T', 'U', 'V'];
            foreach ($moneyCols as $col) {
                $sheet->setCellValue(
                    "{$col}{$totalRow}",
                    "=SUMIF(\$B\$4:\$B\${$lastRow},\"<>Cancelada\",{$col}4:{$col}{$lastRow})"
                );
                $sheet->getStyle("{$col}{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
            }

            // ===== Freeze panes =====
            $sheet->freezePane('C4');

            // ===== Anchos =====
            $widths = [
                'A' => 16, 'B' => 12, 'C' => 22, 'D' => 16, 'E' => 24,
                'F' => 14, 'G' => 14, 'H' => 18, 'I' => 16, 'J' => 45,
                'K' => 8,
                'L' => 14, 'M' => 12, 'N' => 16, 'O' => 14, 'P' => 14,
                'Q' => 14, 'R' => 16, 'S' => 20,
                'T' => 12, 'U' => 12, 'V' => 14,
                'W' => 16, 'X' => 18,
            ];
            foreach ($widths as $col => $w) {
                $sheet->getColumnDimension($col)->setWidth($w);
            }

            for ($i = 4; $i <= $lastRow; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(45);
            }

            // AutoFilter
            $sheet->setAutoFilter("A3:{$lastCol}{$lastRow}");
        },
    ];
}
}