<?php

namespace App\Imports;

use App\Models\ContractSubsetEmployee;
use App\Models\Employee;
use App\Models\User;
use App\Rules\NationalCodeChecker;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Row;

class NewContractEmployee implements OnEachRow,WithValidation,SkipsOnFailure,WithStartRow,SkipsEmptyRows
{
    use Importable, SkipsFailures;
    private int $contract_subset_id;

    public function __construct($contract_subset_id){
        $this->contract_subset_id = $contract_subset_id;
    }

    public function onRow(Row $row)
    {
        if (Employee::employee($row[4]) == null) {
            $start = explode("/", $row[23]);
            $end = explode("/", $row[24]);
            $employee = Employee::query()->create([
                    "contract_id" => $this->contract_subset_id,
                    "user_id" => Auth::id(),
                    "first_name" => $row[1],
                    "last_name" => $row[2],
                    "gender" => $row[3] == "مرد" ? "m" : "f",
                    "national_code" => $row[4],
                    "id_number" => $row[5],
                    "father_name" => $row[6],
                    "birth_date" => Str::replace(["۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹", "۰"], [1, 2, 3, 4, 5, 6, 7, 8, 9, 0], $row[7]),
                    "birth_city" => $row[8],
                    "issue_city" => $row[8],
                    "education" => $row[12],
                    "marital_status" => $row[13] == "متاهل" ? "m" : "s",
                    "children_count" => $row[14] ? $row[14] : 0,
                    "included_children_count" => $row[14] ? $row[14] : 0,
                    "insurance_number" => $row[15],
                    "insurance_days" => $row[16],
                    "military_status" => ($row[17] == "کارت پایان خدمت" ? "h" : $row[17] == "معاف") ? "e" : "n",
                    "job_seating" => $row[10],
                    "job_title" => $row[11],
                    "bank_name" => $row[33],
                    "bank_account" => $row[34],
                    "sheba_number" => $row[35],
                    "phone" => $row[19],
                    "mobile" => $row[18],
                    "address" => $row[20],
                    "documents" => 1,
                    "initial_start" => implode("-", Verta::jalaliToGregorian(intval($start[0]), intval($start[1]), intval($start[2]))),
                    "initial_end" => implode("-", Verta::jalaliToGregorian(intval($end[0]), intval($end[1]), intval($end[2]))),
                ]
            );
            if ($row[25] != "0000-00-00" && $row[26] != "0000-00-00") {
                $employee->contract_extensions()->create([
                    "user_id" => Auth::id(),
                    "start" => $row[25],
                    "active" => 1,
                    "end" => $row[26]
                ]);
            }
            $email = null;
            if (filter_var($row[37], FILTER_VALIDATE_EMAIL)) {
                $dup = User::query()->where("email", "=", $row[37])->get();
                if ($dup->isEmpty())
                    $email = $row[37];
            }
            $username = $employee->national_code;
            if ($row[38]) {
                $udup = User::query()->where("username", "=", $row[38])->get();
                if ($udup->isEmpty())
                    $username = $row[38];
            }
            $duplicate = User::query()->where("username",$username)->get();
            if ($duplicate->isNotEmpty())
                $username = $username.$username;
            User::query()->create([
                "user_id" => Auth::id(),
                "employee_id" => $employee->id,
                "name" => $employee->name,
                "username" => $username,
                "password" => $row[39] ? Hash::make($row[39]) : Hash::make($employee->national_code),
                "email" => $email,
                "gender" => $employee->gender,
                "is_super_user" => 0,
                "is_admin" => 0,
                "is_staff" => 0,
                "is_user" => 1,
            ]);
        }

    }

    #[ArrayShape(["4" => "\App\Rules\NationalCodeChecker[]"])] public function rules(): array
    {
        return [
            "4" => [new NationalCodeChecker()]
        ];
    }

    #[ArrayShape(['4' => "string"])] public function customValidationAttributes(): array
    {
        return ['4' => 'national_code'];
    }

    public function prepareForValidation($data)
    {
        if(strlen($data[4]) == 8)
            $data[4] = "00". $data[4];
        elseif (strlen($data[4]) == 9)
            $data[4] = "0" . $data[4];
        return $data;
    }


    public function startRow(): int
    {
        return 2;
    }

}
