<?php

namespace App\Imports;

use App\Models\ContractSubsetEmployee;
use App\Rules\NationalCodeChecker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use JetBrains\PhpStorm\ArrayShape;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class ContractEmployeesImport implements OnEachRow,WithValidation,SkipsOnFailure,WithEvents,WithStartRow,SkipsEmptyRows
{
    use Importable, SkipsFailures;
    private int $contract_subset_id;

    public function __construct($contract_subset_id){
        $this->contract_subset_id = $contract_subset_id;
    }

    public function onRow(Row $row)
    {
        ContractSubsetEmployee::query()->create([
                "contract_subset_id" => $this->contract_subset_id,
                "user_id" => Auth::id(),
                "name" => str_replace("ي","ی",$row[0]),
                "national_code" => $row[1],
                "mobile" => $row[2]
            ]
        );
    }

    #[ArrayShape(["1" => "\App\Rules\NationalCodeChecker[]"])] public function rules(): array
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
        if(strlen($data[1]) == 8)
            $data[1] = "00". $data[1];
        elseif (strlen($data[1]) == 9)
            $data[1] = "0" . $data[1];
        return $data;
    }

    #[ArrayShape([BeforeImport::class => "\Closure"])] public function registerEvents(): array
    {
        return [
            BeforeImport::class => function(BeforeImport $event) {
                $properties = $event->getDelegate()->getProperties();
                $worksheet = $event->reader->getActiveSheet();
                $highestRow = $worksheet->getHighestRow();
                if (!Hash::check("hss_creator",$properties->getCreator()) || !Hash::check("hss_manager",$properties->getManager()) || !Hash::check("hss",$properties->getCompany()))
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
}
