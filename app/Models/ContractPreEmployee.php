<?php

namespace App\Models;

use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ContractPreEmployee extends Model
{
    use HasFactory;
    protected $table = "contract_pre_employees";
    protected $fillable = ["contract_id","user_id","approved","name","national_code","mobile","verify","verify_timestamp","tracking_code","registered","registration_date","reload_date"];

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
        return self::with(["contract.organization"])->where("national_code","=",$national_code)->first();
    }
    public function reload_data(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(ReloadEmployeeData::class, 'reloadable');
    }
    public static function NewRegistration($contracts)
    {
        $employees = self::query()->with(["contract","reload_data"])->where("registered","=",1)
            ->where("tracking_code","<>",null)->whereHas("contract",function($query) use($contracts){
            $query->whereIn("contract_pre_employees.contract_id",$contracts);
        })->where("approved","=",0)->orderBy("updated_at","desc")->get();
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
            $information ? $employee["flag"] = 1 : $employee["flag"] = 0;
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
    public static function NewRegistrationPaginate($contracts)
    {
        $employees = self::query()->with(["contract.organization","reload_data"])->where("registered","=",1)
            ->where("tracking_code","<>",null)->whereHas("contract",function($query) use($contracts){
                $query->whereIn("contract_pre_employees.contract_id",$contracts);
            })->where("approved","=",0)->orderBy("updated_at","desc")->get();
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
            $information ? $employee["flag"] = 1 : $employee["flag"] = 0;
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

    public static function statistics(): array
    {
        $result = [];
        for($i = 6; $i >= 0; $i--){
            $date = verta()->subMonths($i);
            $last_month_day = $date->daysInMonth;
            $year = $date->year;
            $month = $date->month;
            $from_date = gmdate("Y/m/d H:i:s",Verta::createJalali($year,$month,1,0,0,0)->timestamp);
            $to_date = gmdate("Y/m/d H:i:s",Verta::createJalali($year,$month,$last_month_day,0,0,0)->timestamp);
            $month_name = $date->format("F");
            $result[] = [
                "month" => $month_name,
                "count" => self::query()->whereDate("registration_date",">=",$from_date)->whereDate("registration_date","<=",$to_date)->count()
            ];
        }
        return $result;
    }

    public function RegisteredMessaging(): array
    {
        $result["message"]["users"] = User::RecipientRegistration($this->contract_id);
        $result["message"]["data"]["message"] = "رکورد جدید در ثبت نام پرسنل دریافت شد";
        $result["message"]["data"]["type"] = "register";
        $result["message"]["data"]["action"] = route("EmployeesRecruiting.index");
        return $result;
    }
    public static function UnRegisteredMessaging($organization_id): array
    {
        $result["message"]["users"] = User::RecipientPreRegistration($organization_id);
        $result["message"]["data"]["message"] = "رکورد جدید در پیش ثبت نام پرسنل دریافت شد";
        $result["message"]["data"]["type"] = "preRegister";
        $result["message"]["data"]["action"] = route("UnregisteredEmployees.index");
        return $result;
    }
    public function ReloadMessaging(): array
    {
        $result["message"]["users"] = User::RecipientReloading($this->contract_id);
        $result["message"]["data"]["message"] = "رکورد جدید در اصلاح اطلاعات پرسنل دریافت شد";
        $result["message"]["data"]["type"] = "refresh";
        $result["message"]["data"]["action"] = route("RefreshDataEmployees.index");
        return $result;
    }
    public static function find($keyword): \Illuminate\Database\Eloquent\Collection|array
    {
        return ContractPreEmployee::query()->where("name","like",$keyword)->orWhere("national_code","like",$keyword)->get();
    }
}
