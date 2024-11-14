<?php

namespace App\Imports;

use App\Models\ContractPreEmployee;
use App\Models\Employee;
use App\Rules\NationalCodeChecker;
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

class PreContractEmployeeImport implements OnEachRow,WithValidation,SkipsOnFailure,WithEvents,WithStartRow,SkipsEmptyRows
{
    use Importable, SkipsFailures;
    private array $result = ["success" => [],"fail" => []];

    #[ArrayShape(["1" => "array"])] public function rules(): array
    {
        return [
            "1" => [new NationalCodeChecker()]
        ];
    }

    #[ArrayShape(['1' => "string"])] public function customValidationAttributes(): array
    {
        return ['1' => 'national_code'];
    }


    public function prepareForValidation($data)
    {
        if (strlen($data[1]) == 8)
            $data[1] = "00" . $data[1];
        elseif (strlen($data[1]) == 9)
            $data[1] = "0" . $data[1];
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
                    throw new \Exception("خطا در شناسایی فایل اکسل: لطفا اطلاعات پرسنل را فقط در فایل نمونه دریافت شده به صورت مقدار (paste as value) جایگذاری نموده و سپس اقدام به ارسال نمایید");
                elseif ($highestRow == 1 || $highestRow == 0)
                    throw new \Exception("فایل اکسل بارگذاری شده خالی می باشد");

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

    public function onRow(Row $row)
    {
        if ($employee = ContractPreEmployee::employee($row[1]))
            $this->result["fail"][] = ["row" => $row->getRowIndex(), "message" => "کد ملی وارد شده در لیست پیش ثبت نام در ". $employee->contract->name . " " . $employee->contract->organization->name . " ایجاد شده است", "national_code" => $row[1]];
        elseif ($employee = Employee::employee($row[1]))
            $this->result["fail"][] = ["row" => $row->getRowIndex(), "message" => "کد ملی وارد شده در " . $employee->contract->name . " " . $employee->contract->organization->name . " ثبت نام شده است", "national_code" => $row[1]];
        elseif (in_array($row[1],array_column($this->result["success"],"national_code")) || in_array($row[1],array_column($this->result["fail"],"national_code")))
            $this->result["fail"][] = ["row" => $row->getRowIndex(), "message" => "کد ملی وارد شده در فایل تکراری می باشد", "national_code" => $row[1]];
        elseif(!in_array($row[1],array_column($this->result["success"],"national_code")) && !in_array($row[1],array_column($this->result["fail"],"national_code")))
            $this->result["success"][] = ["name" => $row[0], "national_code" => $row[1], "mobile" => $row[2]];
        else
            $this->result["fail"][] = ["row" => $row->getRowIndex(), "message" => "کد ملی وارد شده موجود نمی باشد", "national_code" => $row[1]];

    }
}
