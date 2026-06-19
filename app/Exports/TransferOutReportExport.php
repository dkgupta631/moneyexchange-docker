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

class TransferOutReportExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected ?string $statusFilter;
    private int $rowIndex = 0;

    public function __construct(?string $statusFilter = null)
    {
        $this->statusFilter = $statusFilter;
    }

    public function query(): Builder
    {
        return MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-OUT')
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->orderBy('id', 'desc');
    }

    public function title(): string
    {
        return 'Transfer-OUT Report';
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
            $row->created_at?->format('d M Y  H:i') ?? '—',
            $row->customer_name ?? '—',
            $row->bank_name ?? '—',
            $row->acc_name ?? '—',
            $row->acc_number ?? '—',
            match($row->status) {
                'completed'            => 'Completed',
                'pending_bkk_approval' => 'Pending BKK',
                'accepted_bkk'         => 'Accepted BKK',
                'Rejected'             => 'Rejected',
                'cancelled'            => 'Cancelled',
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
            'B' => 26,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 18,
            'G' => 16,
            'H' => 18,
            'I' => 18,
            'J' => 16,
            'K' => 16,
            'L' => 22,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $numCols = 'L';

                // 1. Insert title row
                $sheet->insertNewRowBefore(1, 1);
                $lastRow += 1;  

                // 2. Gold title banner
                $sheet->setCellValue(
                    'A1',
                    __('message.transfer_out_report') . ' | ' .
                    __('message.All Records') . ' | ' .
                    __('message.Generated') . ': ' . now()->format('d M Y H:i')
                );
                $sheet->mergeCells("A1:{$numCols}1");
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFD4920A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(30);

                // 3. Navy heading row
                $sheet->getStyle("A2:{$numCols}2")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FF0E1F3D']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(22);

                // 4. Data rows
                $dataStart  = 3;
                $dataEnd    = $lastRow;
                $amountCols = ['I', 'J', 'K'];

                for ($r = $dataStart; $r <= $dataEnd; $r++) {
                    $bgColor = ($r % 2 === 0) ? 'FFF0F4FA' : 'FFFFFFFF';
                    $sheet->getStyle("A{$r}:{$numCols}{$r}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => $bgColor]],
                    ]);
                    $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    foreach ($amountCols as $col) {
                        $sheet->getStyle("{$col}{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        $sheet->getStyle("{$col}{$r}")->getNumberFormat()->setFormatCode('#,##0.00');
                    }

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
                        'font'      => ['bold' => true, 'color' => ['argb' => $statusColor]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }

                // 5. Totals — Transfer-OUT completed only
                $totals = MoneyTransferInvoice::query()
                    ->where('transfer_type', 'Transfer-OUT')
                    ->where('status', 'completed')
                    ->selectRaw('
                        SUM(CAST(entered_amount AS DECIMAL(15,2))) as total_entered,
                        SUM(CAST(trf_fee        AS DECIMAL(15,2))) as total_fee,
                        SUM(CAST(net_amount     AS DECIMAL(15,2))) as total_net,
                        COUNT(*) as total_count
                    ')
                    ->first();

                $bannerRow = $dataEnd + 2;
                $labelRow  = $bannerRow + 1;
                $valueRow  = $bannerRow + 2;

                $sheet->setCellValue("A{$bannerRow}",  __('message.total_completed_records'));
                $sheet->mergeCells("A{$bannerRow}:{$numCols}{$bannerRow}");
                $sheet->getStyle("A{$bannerRow}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FF162D56']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($bannerRow)->setRowHeight(24);

                $sheet->setCellValue("A{$labelRow}", __('message.Completed Records'));
                $sheet->mergeCells("A{$labelRow}:H{$labelRow}");
                $sheet->getStyle("A{$labelRow}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF0E1F3D']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFDCE5F5']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $totalsMap = [
                    'I' => ['label' =>  __('message.Total Entered Amount'), 'value' => (float) ($totals->total_entered ?? 0)],
                    'J' => ['label' => __('message.Total Transfer Fee'),   'value' => (float) ($totals->total_fee ?? 0)],
                    'K' => ['label' => __('message.Total Net Amount'),     'value' => (float) ($totals->total_net ?? 0)],
                ];

                foreach ($totalsMap as $col => $data) {
                    $sheet->setCellValue("{$col}{$labelRow}", $data['label']);
                    $sheet->getStyle("{$col}{$labelRow}")->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF0E1F3D']],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFDCE5F5']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }

                $sheet->setCellValue("A{$valueRow}", ($totals->total_count ?? 0) . ' records');
                $sheet->mergeCells("A{$valueRow}:H{$valueRow}");
                $sheet->getStyle("A{$valueRow}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FF0E1F3D']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFFEEFC3']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($valueRow)->setRowHeight(28);

                foreach ($totalsMap as $col => $data) {
                    $sheet->setCellValue("{$col}{$valueRow}", $data['value']);
                    $sheet->getStyle("{$col}{$valueRow}")->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF0E1F3D']],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFF5B014']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FFD4920A']]],
                    ]);
                    $sheet->getStyle("{$col}{$valueRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getRowDimension($valueRow)->setRowHeight(28);
                }

                $sheet->freezePane('A3');
                $sheet->setAutoFilter("A2:{$numCols}{$dataEnd}");
                $sheet->getStyle("A2:{$numCols}{$dataEnd}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFB8CAEA']]],
                ]);
            },
        ];
    }
}