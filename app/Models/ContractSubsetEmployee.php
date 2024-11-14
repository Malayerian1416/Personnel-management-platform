<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use function Clue\StreamFilter\fun;
use function Psl\Vec\map;
use function VeeWee\Xml\Xslt\Configurator\functions;

class ContractSubsetEmployee extends Model
{
    use HasFactory;
    protected $table = "contract_subset_employees";
    protected $fillable = ["contract_subset_id","user_id","name","national_code","mobile","verify","verify_timestamp","tracking_code","registered","registration_date","to_reload","reloaded","reload_date"];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }

    public function contract(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Contract::class,"contract_id");
    }

    public static function employee($national_code): Model|\Illuminate\Database\Eloquent\Builder|null
    {
        return self::with(["contract" => function($query){
            $query->where("inactive","=",0)->where("deleted_at","=",null);
        }])->where("national_code","=",$national_code)->first();
    }

    public function reload_data(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(ReloadEmployeeData::class, 'reloadable');
    }

    public static function NewRegistration($contracts): \Illuminate\Database\Eloquent\Collection|array
    {
        $employees = self::query()->with(["contract"])->where("registered","=",1)->where("tracking_code","<>",null)->whereHas("contract",function($query) use($contracts){
            $query->whereIn("contract_subset_employees.contract_subset_id",$contracts);
        })->where("to_reload","=",0)->get();
        $employees->map(function($employee){
            $information = Employee::query()->where("national_code","=",$employee->national_code)->first([
                "id",
                "first_name",
                "last_name",
                "gender",
                "national_code",
                "id_number",
                "father_name",
                "birth_date",
                "birth_city",
                "issue_city",
                "education",
                "marital_status",
                "children_count",
                "included_children_count",
                "insurance_number",
                "insurance_days",
                "military_status",
                "bank_name",
                "bank_account",
                "credit_card",
                "sheba_number",
                "phone",
                "mobile",
                "address",
                "job_seating",
                "job_title"
            ]);
            $docs = Storage::disk("employee_docs")->exists($employee->national_code);
            $information && $docs ? $employee["flag"] = 1 : $employee["flag"] = 0;
            if ($employee["flag"] == 1){
                $employee["database"] = $information->toArray();
                $paths = Storage::disk("employee_docs")->allFiles("$employee->national_code");
                $employee_docs = [];
                foreach ($paths as $path) {
                    $path = str_replace("/","@",$path);
                    $employee_docs[] = ["view" => route("docs.image_view", ["path" => $path]) , "print" => route("print_docs", ["path" => $path])];
                }
                $employee["docs"] = $employee_docs;
            }
        });
        return $employees->where("flag","=",1)->values();
    }

}
