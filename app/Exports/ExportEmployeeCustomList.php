<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
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

class ExportEmployeeCustomList extends StringValueBinder implements FromView,WithStyles,WithEvents,WithTitle,WithProperties
{
    public mixed $EmployeeList;
    public array $titles;

    public function __construct($EmployeeList,$titles)
    {
        $this->EmployeeList = $EmployeeList;
        $this->titles = $titles;
    }

    public function styles(Worksheet $sheet): void
    {
        $columns = ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA"];
        $column = $columns[count($this->titles)];
        $sheet->getStyle("A:$column")->getFont()->setName('tahoma');
        $sheet->getStyle("A:$column")->getFont()->setSize(9);
        $sheet->getStyle("A1:{$column}1")->getFont()->setSize(9);
        $sheet->getStyle("A:$column")->getAlignment()->setVertical('center');
        $sheet->getStyle("A:$column")->getAlignment()->setHorizontal('center');
        for ($i = 0; $i < count($this->titles); $i++)
            $sheet->getColumnDimension($columns[$i])->setWidth(20);
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
        return view('layouts.excel.employee_custom_list',["titles" => $this->titles, "employees" => $this->EmployeeList]);
    }

    public function title(): string
    {
        return "لیست پرسنل";
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
