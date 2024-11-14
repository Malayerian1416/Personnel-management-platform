<?php

namespace App\Exports;

use App\Models\Contract;
use Exception;
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

class ExportContractPaySlip extends StringValueBinder implements FromView,WithStyles,WithEvents,WithTitle,WithProperties
{
    public int $contract_id;
    public mixed $template;
    public mixed $contract;

    public function __construct($contract_id){
        $this->contract_id = $contract_id;
        $this->contract = Contract::query()->with(["payslip_template","organization"])->findOrFail($contract_id);
        $this->template = $this->contract->payslip_template;
    }

    public function styles(Worksheet $sheet)
    {
        $columns = json_decode($this->template->columns, true);
        $last = $columns[count($columns) - 1];
        $sheet->getStyle("A:{$last['column']}")->getFont()->setName('tahoma');
        $sheet->getStyle("A:{$last['column']}")->getFont()->setSize(9);
        $sheet->getStyle("A1:{$last['column']}1")->getFont()->setSize(9);
        $sheet->getStyle("A:{$last['column']}")->getAlignment()->setVertical('center');
        $sheet->getStyle("A:{$last['column']}")->getAlignment()->setHorizontal('center');
        foreach ($columns as $column) {
            if ($column["ignore"])
                $sheet->getColumnDimension("{$column['column']}")->setWidth(10);
            else
                $sheet->getColumnDimension("{$column['column']}")->setWidth(25);
        }
    }
    #[ArrayShape([AfterSheet::class => "\Closure"])] public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
            },
        ];
    }

    /**
     * @throws Exception
     */
    public function view(): View
    {
        if ($this->template == null)
            throw new Exception("قرارداد انتخاب شده دارای قالب فیش حقوقی نمی باشد");
        else
            return view('layouts.excel.payslip_template',["template" => json_decode($this->template->columns,true)]);
    }

    public function title(): string
    {
        return "قرارداد " . "{$this->contract->name}({$this->contract->organization->name})";
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
