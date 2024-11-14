<?php

namespace App\Imports;

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

class ImportContractDateExcel implements OnEachRow,WithValidation,SkipsOnFailure,WithEvents,WithStartRow,SkipsEmptyRows
{
    use Importable, SkipsFailures;
    private array $result = ["success" => [],"fail" => []];

    #[ArrayShape(["0" => "\App\Rules\NationalCodeChecker[]", "1" => "string[]", "2" => "string[]"])] public function rules(): array
    {
        return [
            "0" => [new NationalCodeChecker()],
            "1" => ["jdate:Y/m/d"],
            "2" => ["jdate:Y/m/d"]
        ];
    }

    #[ArrayShape(["1.jdate" => "string", "2.jdate" => "string"])] public function customValidationMessages(): array
    {
        return [
            "1.jdate" => "تاریخ شروع قرارداد صحیح نمی باشد(مثلا: 1400/01/01)",
            "2.jdate" => "تاریخ پایان قرارداد صحیح نمی باشد(مثلا: 1400/01/01)"
        ];
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

    public function prepareForValidation($data)
    {
        if (strlen($data[0]) == 8)
            $data[0] = "00" . $data[0];
        elseif (strlen($data[0]) == 9)
            $data[0] = "0" . $data[0];
        return $data;
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
        $employee = Employee::employee($row[0]);
        if ($employee != null) {
            if (verta($row[1])->greaterThan(verta($row[2])))
                $this->result["fail"][] = ["row" => $row->getRowIndex(), "message" => "تاریخ پایان قرارداد باید پس از تاریخ شروع قرارداد درج شود", "value" => $row[0]];
            else
                $this->result["success"][] = ["id" => $employee->id, "name" => $employee->name, "national_code" => $employee->national_code, "contract" => $employee->contract->name, "start" => $row[1], "end" => $row[2]];
        }
        else
            $this->result["fail"][] = ["id" => $row->getRowIndex(), "message" => "کد ملی وارد شده معتبر نمی باشد", "value" => $row[0]];
    }
}
