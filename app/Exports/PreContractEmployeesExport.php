<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use JetBrains\PhpStorm\ArrayShape;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PreContractEmployeesExport extends StringValueBinder implements FromView,WithStyles,WithEvents,WithTitle,WithProperties
{
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A:C')->getFont()->setName('tahoma');
        $sheet->getStyle('A:C')->getFont()->setSize(9);
        $sheet->getStyle('A1:C1')->getFont()->setSize(9);
        $sheet->getStyle('A:C')->getAlignment()->setVertical('center');
        $sheet->getStyle('A:C')->getAlignment()->setHorizontal('center');
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(25);
    }
    #[ArrayShape([AfterSheet::class => "\Closure"])] public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
            },
        ];
    }
    public function view(): View
    {
        return view('layouts.excel.contract_employees');
    }

    public function title(): string
    {
        return "لیست پرسنل قرارداد";
    }

    #[ArrayShape(['creator' => "string", 'manager' => "string", 'company' => "string"])] public function properties(): array
    {
        return [
            'creator' => Hash::make("hss_creator"),
            'manager' => Hash::make("hss_manager"),
            'company' => Hash::make("hss"),
        ];
    }
}
