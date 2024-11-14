<?php

namespace App\Imports;

use App\Rules\NationalCodeChecker;
use \Hekmatinasser\Verta\Verta;
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

class ImportNewEmployee implements OnEachRow,WithValidation,SkipsOnFailure,WithEvents,WithStartRow,SkipsEmptyRows
{
    use Importable, SkipsFailures;
    private array $result = ["success" => [],"fail" => []];

    #[ArrayShape(["2" => "array", "3" => "string[]", "4" => "string[]", "5" => "string[]"])] public function rules(): array
    {
        return [
            "2" => ["required" , new NationalCodeChecker(),"unique:employees,national_code"],
            "3" => ["sometimes" , "nullable" , "regex:/^09(1[0-9]|9[0-2]|2[0-2]|0[1-5]|41|3[0,3,5-9])\d{7}$/", "unique:employees,mobile"],
            "4" => ["required" , "jdate:Y/m/d"],
            "5" => ["required" , "jdate:Y/m/d"]
        ];
    }


    #[ArrayShape(["2.required" => "string", "2.unique" => "string", "3.regex" => "string", "3.unique" => "string", "4.required" => "string", "4.jdate" => "string", "5.required" => "string", "5.jdate" => "string", "5.jdate_after" => "string"])] public function customValidationMessages()
    {
        return [
            "2.required" => "کد ملی وارد نشده است",
            "2.unique" => "کد ملی قبلا ثبت شده است",
            "3.regex" => "شماره موبایل درج شده صحیح نمی باشد",
            "3.unique" => "شماره موبایل قبلا ثبت شده است",
            "4.required" => "تاریخ شروع قرارداد وارد نشده است",
            "4.jdate" => "تاریخ شروع قرارداد صحیح نمی باشد(مثلا: 1400/01/01)",
            "5.required" => "تاریخ پایان قرارداد وارد نشده است",
            "5.jdate" => "تاریخ پایان قرارداد صحیح نمی باشد(مثلا: 1400/01/01)"
        ];
    }

    public function prepareForValidation($data)
    {
        if (strlen($data[2]) == 8)
            $data[2] = "00" . $data[2];
        elseif (strlen($data[2]) == 9)
            $data[2] = "0" . $data[2];
        if (strlen($data[3]) == 10)
            $data[3] = "0" . $data[3];
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

    public function onRow(Row $row): void
    {
        if (in_array($row[2],array_column($this->result["success"],"national_code")) || in_array($row[2],array_column($this->result["fail"],"national_code")))
            $this->result["fail"][] = ["row" => $row->getRowIndex(), "message" => "کد ملی وارد شده در فایل تکراری می باشد", "national_code" => $row[2]];
        elseif (verta($row[4])->greaterThan(verta($row[5])))
            $this->result["fail"][] = ["row" => $row->getRowIndex(), "message" => "تاریخ پایان قرارداد باید پس از تاریخ شروع قرارداد درج شود", "national_code" => $row[2]];
        else {
            $this->result["success"][] = ["first_name" => $row[0], "last_name" => $row[1], "national_code" => $row[2], "mobile" => $row[3], "initial_start" => $row[4], "initial_end" => $row[5]];
        }
    }
}
