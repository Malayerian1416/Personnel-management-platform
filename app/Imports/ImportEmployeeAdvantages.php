<?php

namespace App\Imports;

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
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Row;

class ImportEmployeeAdvantages implements OnEachRow,WithValidation,SkipsOnFailure,WithEvents,WithStartRow,SkipsEmptyRows,WithHeadings
{
    use Importable, SkipsFailures;
    private array $result = ["success" => [],"fail" => []];
    private int $contract_id;

    public function __construct($contract_id){
        $this->contract_id = $contract_id;
    }

    #[ArrayShape(["0" => "\App\Rules\NationalCodeChecker[]"])] public function rules(): array
    {
        return [
            "0" => [new NationalCodeChecker()]
        ];
    }

    #[ArrayShape(["0" => "string"])] public function customValidationAttributes(): array
    {
        return ["0" => 'national_code'];
    }


    public function prepareForValidation($data)
    {
        if (strlen($data[0]) == 8)
            $data[0] = "00" . $data[0];
        elseif (strlen($data[0]) == 9)
            $data[0] = "0" . $data[0];
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

    public function getTitles(): array
    {
        return $this->columns;
    }

    /**
     * @throws Exception
     */
    public function onRow(Row $row)
    {
        $employee = Employee::employee($row[0]);
        if ($employee == null)
            $this->result["fail"][] = ["row" => $row->getRowIndex(), "message" => "کد ملی وارد شده موجود نمی باشد", "national_code" => $row[0]];
        elseif (in_array($row[0],array_column($this->result["success"],"national_code")) || in_array($row[0],array_column($this->result["fail"],"national_code")))
            $this->result["fail"][] = ["row" => $row->getRowIndex(), "message" => "کد ملی وارد شده تکراری می باشد", "national_code" => $row[0]];
        elseif ($employee->contract_id != $this->contract_id)
            $this->result["fail"][] = ["row" => $row->getRowIndex(), "message" => "کد ملی وارد شده زیرمجموعه "."{$employee->contract->name}({$employee->contract->organization->name})"." می باشد", "national_code" => $row[0]];
        else {
            $employee_row = [
                "id" => $employee->id,
                "name" => $employee->name,
                "national_code" => $employee->national_code,
                "daily_wage" => $row[1],
                "prior_service" => $row[2],
                "working_days" => $row[3],
                "occupational_group" => $row[4],
                "count_of_children" => $row[5],
                "advantages" => []
            ];
            foreach ($row->toArray() as $key => $value) {
                if ($key < 6)
                    continue;
                if ($value == null || $value == "")
                    $value = 0;
                $employee_row["advantages"][] = [
                    "title" => $key,
                    "value" => $value,
                ];
            }
            $this->result["success"][] = $employee_row;
        }
    }

    public function headings(): array
    {

    }
}
