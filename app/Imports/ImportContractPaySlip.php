<?php

namespace App\Imports;

use App\Models\Contract;
use App\Models\Employee;
use App\Rules\NationalCodeChecker;
use Exception;
use Illuminate\Support\Facades\Hash;
use JetBrains\PhpStorm\ArrayShape;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Row;

class ImportContractPaySlip implements OnEachRow,WithValidation,SkipsOnFailure,WithEvents,WithStartRow,SkipsEmptyRows
{

    use Importable, SkipsFailures;
    private array $result = ["success" => [],"fail" => []];
    private int $contract_id;
    private mixed $template;
    private mixed $contract;
    private mixed $national_code_index;

    public function __construct($contract_id){
        $this->contract_id = $contract_id;
        $this->contract = Contract::query()->with(["payslip_template","organization"])->findOrFail($contract_id);
        $this->template = $this->contract->payslip_template;
        $this->national_code_index = $this->template->national_code_index;
    }

    public function rules(): array
    {
        return [
            $this->national_code_index => [new NationalCodeChecker()]
        ];
    }

    public function customValidationAttributes(): array
    {
        return [$this->national_code_index => 'national_code'];
    }


    public function prepareForValidation($data)
    {
        if (strlen($data[$this->national_code_index]) == 8)
            $data[$this->national_code_index] = "00" . $data[$this->national_code_index];
        elseif (strlen($data[$this->national_code_index]) == 9)
            $data[$this->national_code_index] = "0" . $data[$this->national_code_index];
        return $data;
    }

    #[ArrayShape([BeforeImport::class => "\Closure"])] public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $properties = $event->getDelegate()->getProperties();
                $worksheet = $event->reader->getActiveSheet();
                $highestRow = $worksheet->getHighestRow();
                if (!Hash::check("hss_creator", $properties->getCreator()) || !Hash::check("hss_manager", $properties->getManager()) || !Hash::check("hss", $properties->getCompany()))
                    throw new Exception("خطا در شناسایی فایل اکسل: لطفا اطلاعات پرسنل را فقط در فایل نمونه دریافت شده به صورت مقدار (paste as value) جایگذاری نموده و سپس اقدام به ارسال نمایید");
                elseif ($highestRow == 1 || $highestRow == 0)
                    throw new Exception("فایل اکسل بارگذاری شده خالی می باشد");

            },
        ];
    }

    public function startRow(): int
    {
        return 2;
    }

    public function getResult(): array
    {
        return $this->result["success"];
    }

    public function getFails(): array
    {
        return $this->result["fail"];
    }


    /**
     * @throws Exception
     */
    public function onRow(Row $row)
    {
        if (count($row->toArray()) != count(json_decode($this->template->columns,true)))
            throw new Exception("تعداد ستون های فایل اکسل با الگوی قالب مطابقت ندارد");
        $employee = Employee::employee($row[$this->national_code_index]);
        if ($employee == null)
            $this->result["fail"][] = ["row" => $row->getRowIndex(), "message" => "کد ملی وارد شده موجود نمی باشد", "national_code" => $row[$this->national_code_index]];
        elseif (in_array($row[$this->national_code_index],array_column($this->result["success"],"national_code")) || in_array($row[$this->national_code_index],array_column($this->result["fail"],"national_code")))
            $this->result["fail"][] = ["row" => $row->getRowIndex(), "message" => "کد ملی وارد شده تکراری می باشد", "national_code" => $row[$this->national_code_index]];
        elseif ($employee->contract_id != $this->contract_id)
            $this->result["fail"][] = ["row" => $row->getRowIndex(), "message" => "کد ملی وارد شده زیرمجموعه "."{$employee->contract->name}({$employee->contract->organization->name})"." می باشد", "national_code" => $row[$this->national_code_index]];
        else {
            $employee_row = [
                "id" => $employee->id,
                "name" => $employee->name,
                "national_code" => $employee->national_code,
                "" => []
            ];
            $columns = json_decode($this->template->columns,true);
            foreach ($columns as $key => $column) {
                if ($column["ignore"] || $key == $this->national_code_index)
                    continue;
                $employee_row["columns"][] = [
                    "column" => $column["column"],
                    "title" => $column['title'],
                    "value" => $row[$key],
                    "type" => $column["type"],
                    "isNumber" => $column["isNumber"]
                ];
            }
            $this->result["success"][] = $employee_row;
        }
    }
}
