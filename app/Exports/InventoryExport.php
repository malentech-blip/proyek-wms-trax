<?php

namespace App\Exports;

use App\Models\Admin\Inventory\Inventory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Inventory::with(['item', 'location'])
            ->orderBy('item_id')
            ->orderBy('location_id')
            ->get();
    }

    /**
     * Define the headings for the Excel file
     */
    public function headings(): array
    {
        return [
            'Item Code',
            'Item Name',
            'Item Type',
            'Unit of Measure',
            'Location Name',
            'Location Code',
            'Quantity',
            'Last Synced At',
        ];
    }

    /**
     * Map the data for each row
     */
    public function map($inventory): array
    {
        return [
            $inventory->item->item_code ?? 'N/A',
            $inventory->item->item_name ?? 'N/A',
            $inventory->item->item_type ?? 'N/A',
            $inventory->item->uom ?? 'N/A',
            $inventory->location->name ?? 'N/A',
            $inventory->location->code ?? 'N/A',
            $inventory->quantity,
            $inventory->last_synced_at ? $inventory->last_synced_at->format('d/m/Y H:i') : 'Never',
        ];
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        // Style the header row
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'], // Blue color
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Style data rows
        $lastRow = $sheet->getHighestRow();
        if ($lastRow > 1) {
            $sheet->getStyle('A2:H'.$lastRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E5E7EB'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Right align quantity column
            $sheet->getStyle('G2:G'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // Set row height for header
        $sheet->getRowDimension(1)->setRowHeight(20);

        return [
            // You can specify additional styles here if needed
            1 => ['font' => ['bold' => true]],
        ];
    }
}
