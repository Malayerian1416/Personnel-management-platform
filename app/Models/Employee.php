<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use function Clue\StreamFilter\fun;

class Employee extends Model
{
    use HasFactory;use softDeletes;
    protected $table = "employees";
    protected $fillable = [
        "contract_id",
        "user_id",
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
        "unemployed",
        "documents",
        "job_seating",
        "job_title",
        "initial_start",
        "initial_end",
        "detached",
    ];
    protected $appends = ["docs","name","gender_refer","gender_word","marital_word","military_word"];

    public function registrant_user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function user(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class,"employee_id","id");
    }
    public function contract(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Contract::class,"contract_id");
    }
    public function contract_extensions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ContractExtension::class,"employee_id");
    }
    public function salary_details(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EmployeeFinancialAdvantage::class,"employee_id");
    }
    public function contract_conversions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ContractConversion::class,"employee_id");
    }
    public function automations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Automation::class,"employee_id");
    }
    public function active_contract_date(): array
    {
        $contract_date = $this->contract_extensions()->where("active","=",1)->first(["start","end"]);
        return $contract_date != null ? $contract_date->toArray() : ["start" => $this->initial_start,"end" => $this->initial_end,"type" => "initial"];
    }

    /**
     * @throws ValidationException
     */
    public function active_salary_details(): array
    {
        $year = verta($this->active_contract_date()["start"])->format("Y");
        $details = $this->salary_details()->where("effective_year","=",$year)->first();
        $labour_law = LabourLawTariff::query()->where("effective_year","=",$year)->first();
        if ($labour_law == null)
            throw ValidationException::withMessages(['employees' => 'تعرفه دستمزد پرسنل برای سال '.$year.' وجود ندارد']);
        $extra_advantages = 0;
        $advantages = [];
        $prior_service = 0;
        $occupational_group = "نامشخص";
        if ($details != null) {
            $advantages = json_decode($details->advantages, true);
            foreach ($advantages as $advantage)
                $extra_advantages += $advantage["value"];
            $daily_wage = $details->daily_wage;
            $prior_service = $details->prior_service;
            $base_salary = $details->working_days * $details->daily_wage;
            $monthly_wage = $base_salary + $details->prior_service;
            $occupational_group = $details->occupational_group;
        }
        else{
            $daily_wage = $labour_law->daily_wage;
            $base_salary = 30 * $labour_law->daily_wage;
            $monthly_wage = $base_salary;
        }
        $child_allowance = $this->included_children_count * $labour_law->child_allowance;
        $housing_purchase_allowance = $labour_law->housing_purchase_allowance;
        $marital_allowance = $this->marital_status == 'm' ? $labour_law->marital_allowance : 0;
        $household_consumables_allowance = $labour_law->household_consumables_allowance;
        $advantage_total = $child_allowance + $marital_allowance + $household_consumables_allowance + $housing_purchase_allowance + $extra_advantages;
        $salary_total = $monthly_wage + $advantage_total;
        return [
            "daily_wage" => $daily_wage,
            "prior_service" => $prior_service,
            "base_salary" => $base_salary,
            "monthly_wage" => $monthly_wage,
            "child_allowance" => $child_allowance,
            "housing_purchase_allowance" => $housing_purchase_allowance,
            "household_consumables_allowance" => $household_consumables_allowance,
            "marital_allowance" => $marital_allowance,
            "advantage_total" => $advantage_total,
            "salary_total" => $salary_total,
            "advantages" => $advantages,
            "occupational_group" => $occupational_group
        ];
    }
    public static function employee($national_code): Model|\Illuminate\Database\Eloquent\Builder|null
    {
        return self::query()->with("contract.organization")->where("national_code","=",$national_code)->first();
    }
    public function payslip(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EmployeePaySlip::class,"employee_id");
    }
    public function payslip_dated($year,$month): Model|\Illuminate\Database\Eloquent\Relations\HasMany|null
    {
        return $this->payslip()->where("year","=",$year)->where("month","=",$month)->first();
    }

    public function tickets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ticket::class,"employee_id");
    }
    public function data_refresh(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(EmployeeDataRequest::class,"employee_id");
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
        return Employee::query()->with(["contract.organization","registrant_user","user"])
                ->where("last_name","like",$keyword)->orWhere("national_code","like",$keyword)->get();
    }
    public static function GetTickets(): array
    {
        $result = [];
        $employees = Employee::query()->whereHas("tickets", function ($query){$query->orderBy("updated_at","desc");})->get();
        $employees->map(function ($employee) use (&$result){
            $tempArr = ["id" => $employee->id, "name" => $employee->name, "national_code" => $employee->national_code, "rooms" => []];
            foreach (array_unique($employee->tickets()->pluck("room_id")->toArray()) as $room){
                $tempArr["rooms"]["room.$room"] = TicketRoom::query()->where("id",$room)->with("tickets")->get();
            }
            $result[] = $tempArr;
        });
        return $result;
    }
    public function getDocsAttribute(): array|string
    {
        $docs = Storage::disk("employee_docs")->exists("/$this->national_code");
        if ($docs){
            $paths = Storage::disk("employee_docs")->allFiles("$this->national_code");
            $employee_docs = [];
            foreach ($paths as $path) {
                $doc = $path;
                $path = str_replace("/","@",$path);
                $employee_docs[] = ["view" => route("docs.image_view", ["path" => $path]) , "print" => route("print_docs", ["path" => $path]), "path" => $doc];
            }
            return $employee_docs;
        }
        return [];
    }
    public function getNameAttribute(): string
    {
        return $this->first_name . " " . $this->last_name;
    }
    public function getGenderReferAttribute(): string
    {
         if($this->gender == "m")
             return "جناب آقای";
         elseif ($this->gender == "f")
             return"سرکار خانم";
         else
             return "جناب آقای/سرکار خانم";
    }
    public function getGenderWordAttribute(): string
    {
        if($this->gender == "m")
            return "مرد";
        elseif ($this->gender == "f")
            return"زن";
        else
            return "مرد/زن";
    }
    public function getMaritalWordAttribute(): string
    {
        if($this->marital_status == "m")
            return "متاهل";
        elseif ($this->marital_status == "s")
            return "مجرد";
        else
            return "نامشخص";
    }
    public function getMilitaryWordAttribute(): string
    {
        if($this->military_status == "h")
            return "کارت پایان خدمت";
        elseif ($this->military_status == "e")
            return "معاف از خدمت";
        else if ($this->military_status == "n")
            return "در حال تحصیل";
        else
            return "نامشخص";
    }
}
