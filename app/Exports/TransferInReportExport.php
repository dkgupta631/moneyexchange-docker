<?php

namespace App\Exports;

use App\Models\MoneyTransferInvoice;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Database\Eloquent\Builder;

class TransferInReportExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected ?string $statusFilter;
    private int $rowIndex = 0;

    public function __construct(?string $statusFilter = null)
    {
        $this->statusFilter = $statusFilter;
    }

    /**
     * ALL Transfer-IN records ordered by id desc.
     * Totals section in Excel is calculated separately for completed only.
     */
    public function query(): Builder
    {
        return MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-IN')
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->orderBy('id', 'desc');
    }

    public function title(): string
    {
        return 'Transfer-IN Report';
    }

    public function headings(): array
    {
        return [
            __('message.Serial number'),
            __('message.Invoice Number'),
            __('message.Date'),
            __('message.Customer name'),
            __('message.Bank Name'),
            __('message.Account Name'),
            __('message.Account Number'),
            __('message.Status'),
            __('message.Amount'),
            __('message.Transfer Fee'),
            __('message.Net Amount'),
            __('message.Reject Reason'),
        ];
    }

    public function map($row): array
    {
        $this->rowIndex++;

        return [
            $this->rowIndex,
            $row->invoice_number ?? '—',
            $row->created_at?->format('d M Y H:i') ?? '—',
            $row->customer_name ?? '—',
            $row->bank_name ?? '—',
            $row->acc_name ?? '—',
            $row->acc_number ?? '—',
            match($row->status) {
                'pending_bkk_approval' => '⏳ ' . __('message.Pending'),
                'accepted_bkk'         => '✅ ' . __('message.Accepted'),
                'completed'            => '✔ '  . __('message.Completed'),
                'Rejected'             => '❌ ' . __('message.Rejected'),
                default                => $row->status ?? '—',
            },
            (float) ($row->entered_amount ?? 0),
            (float) ($row->trf_fee ?? 0),
            (float) ($row->net_amount ?? 0),
            $row->reject_reason ?? '—',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 24,
            'C' => 20,
            'D' => 20,
            'E' => 18,
            'F' => 16,
            'G' => 18,
            'H' => 18,
            'I' => 16,
            'J' => 14,
            'K' => 14,
            'L' => 20,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Heading row will be row 2 after we insert title row in AfterSheet
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow(); // last data row before inserts
                $numCols = 'L';

                // ── 1. Insert 1 title row at top ────────────────────────────
                $sheet->insertNewRowBefore(1, 1);
                $lastRow += 1;

                // ── 2. Title row (row 1) ────────────────────────────────────
                $sheet->setCellValue(
                    'A1',
                    __('message.Transfer-IN Report') . ' | ' .
                    __('message.All Records') . ' | ' .
                    __('message.Generated') . ': ' . now()->format('d M Y H:i')
                );
                $sheet->mergeCells("A1:{$numCols}1");
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFD4920A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(28);

                // ── 3. Style heading row (row 2) ────────────────────────────
                $sheet->getStyle("A2:{$numCols}2")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FF0E1F3D']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(22);

                // ── 4. Data rows: stripe + format amounts + status color ────
                $dataStart = 3;
                $dataEnd   = $lastRow;

                for ($r = $dataStart; $r <= $dataEnd; $r++) {
                    // Stripe
                    $bgColor = ($r % 2 === 0) ? 'FFF0F4FA' : 'FFFFFFFF';
                    $sheet->getStyle("A{$r}:{$numCols}{$r}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => $bgColor]],
                    ]);

                    // # column center
                    $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Amount columns: right-align + number format
                    foreach (['I', 'J', 'K'] as $col) {
                        $sheet->getStyle("{$col}{$r}")->getAlignment()
                              ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        $sheet->getStyle("{$col}{$r}")->getNumberFormat()
                              ->setFormatCode('#,##0.00');
                    }

                    // Status color
                    $statusVal   = $sheet->getCell("H{$r}")->getValue();
                    $statusColor = match($statusVal) {
                        'Completed'    => 'FF16A34A',
                        'Pending BKK'  => 'FFD97706',
                        'Accepted BKK' => 'FF2563EB',
                        'Rejected'     => 'FFDC2626',
                        'Cancelled'    => 'FF6B7280',
                        default        => 'FF374151',
                    };
                    $sheet->getStyle("H{$r}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['argb' => $statusColor]],
                    ]);
                }

                // ── 5. Totals section (completed records only) ──────────────
                $totals = MoneyTransferInvoice::query()
                    ->where('transfer_type', 'Transfer-IN')
                    ->where('status', 'completed')
                    ->selectRaw('
                        SUM(CAST(entered_amount AS DECIMAL(15,2))) as total_entered,
                        SUM(CAST(trf_fee AS DECIMAL(15,2)))        as total_fee,
                        SUM(CAST(net_amount AS DECIMAL(15,2)))     as total_net,
                        COUNT(*) as total_count
                    ')
                    ->first();

                $sectionHeaderRow = $dataEnd + 2;
                $labelsRow        = $sectionHeaderRow + 1;
                $valuesRow        = $sectionHeaderRow + 2;

                // Section header banner
                $sheet->setCellValue("A{$sectionHeaderRow}", __('message.total_completed_records'));
                $sheet->mergeCells("A{$sectionHeaderRow}:{$numCols}{$sectionHeaderRow}");
                $sheet->getStyle("A{$sectionHeaderRow}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FF162D56']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($sectionHeaderRow)->setRowHeight(22);

                // Column labels
                $totalsMap = [
                    'I' => ['label' => __('message.Total Entered Amount'), 'value' => (float) ($totals->total_entered ?? 0)],
                    'J' => ['label' => __('message.Total Transfer Fee'),   'value' => (float) ($totals->total_fee ?? 0)],
                    'K' => ['label' => __('message.Total Net Amount'),     'value' => (float) ($totals->total_net ?? 0)],
                ];

                foreach ($totalsMap as $col => $data) {
                    $sheet->setCellValue("{$col}{$labelsRow}", $data['label']);
                    $sheet->getStyle("{$col}{$labelsRow}")->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF0E1F3D']],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFDCE5F5']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }

                // Count cell in labels row
                $sheet->setCellValue("A{$labelsRow}", __('message.Completed Records'));
                $sheet->mergeCells("A{$labelsRow}:H{$labelsRow}");
                $sheet->getStyle("A{$labelsRow}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF0E1F3D']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFDCE5F5']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Values row
                $sheet->setCellValue("A{$valuesRow}", ($totals->total_count ?? 0) . ' records');
                $sheet->mergeCells("A{$valuesRow}:H{$valuesRow}");
                $sheet->getStyle("A{$valuesRow}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FF0E1F3D']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFFEEFC3']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($valuesRow)->setRowHeight(26);

                foreach ($totalsMap as $col => $data) {
                    $sheet->setCellValue("{$col}{$valuesRow}", $data['value']);
                    $sheet->getStyle("{$col}{$valuesRow}")->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FF0E1F3D']],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFF5B014']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders'   => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_MEDIUM,
                                'color'       => ['argb' => 'FFD4920A'],
                            ],
                        ],
                    ]);
                    $sheet->getStyle("{$col}{$valuesRow}")
                          ->getNumberFormat()
                          ->setFormatCode('#,##0.00');
                    $sheet->getRowDimension($valuesRow)->setRowHeight(26);
                }

                // ── 6. Freeze below heading ─────────────────────────────────
                $sheet->freezePane('A3');

                // ── 7. Auto-filter on heading row ───────────────────────────
                $sheet->setAutoFilter("A2:{$numCols}{$dataEnd}");

                // ── 8. Thin border around all data rows ─────────────────────
                $sheet->getStyle("A2:{$numCols}{$dataEnd}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => 'FFB8CAEA'],
                        ],
                    ],
                ]);
            },
        ];
    }
}