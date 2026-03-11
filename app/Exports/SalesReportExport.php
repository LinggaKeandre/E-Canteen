<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesReportExport implements FromCollection, WithHeadings
{
    protected $data;
    protected $dateRange;
    protected $totalRevenue;
    protected $totalSold;

    public function __construct($data, $dateRange, $totalRevenue, $totalSold)
    {
        $this->data = $data;
        $this->dateRange = $dateRange;
        $this->totalRevenue = $totalRevenue;
        $this->totalSold = $totalSold;
    }

    public function collection()
    {
        return collect($this->data)->map(function ($item) {
            return [
                'Nama Menu' => $item['menu_name'],
                'Varian/Addons' => $item['variant_addons'] ?? '-',
                'Harga' => $item['price'] ?? 0,
                'Qty Terjual' => $item['total_qty'],
                'Total Pendapatan' => $item['total_subtotal'],
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama Menu',
            'Varian/Addons',
            'Harga',
            'Qty Terjual',
            'Total Pendapatan',
        ];
    }
}

