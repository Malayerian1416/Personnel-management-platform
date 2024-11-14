<?php

namespace App\Models;

use Auth;
use Exception;
use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use JetBrains\PhpStorm\ArrayShape;

class Automation extends Model
{
    protected $table = "automation";
    protected $fillable = ["user_id","employee_id","contract_id","current_role_id","automationable_id","automationable_type","flow","is_read","is_finished","editable","current_priority","message"];
    protected $appends = ["application_name","application_class","expiration_date","flow_array"];

    public function automationable(): MorphTo
    {
        return $this->morphTo();
    }
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class,"employee_id");
    }
    public function contract(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Contract::class,"contract_id");
    }
    public function current_role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class,"current_role_id");
    }
    public function signs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AutomationSign::class,"automation_id");
    }
    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AutomationComment::class,"automation_id");
    }
    public static function GetPermitted(): \Illuminate\Database\Eloquent\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator|array
    {
        $automations = Automation::query()->with(["user","automationable","employee.contract.organization","employee.automations.user","employee.automations.signs","employee.automations.automationable","signs.user.role","comments.user.role"])->whereHas("contract",function ($query){
            $query->whereIn("id",Contract::GetPermitted());
        })->where("is_finished","=",0)->orderBy("created_at","desc")->get();
        return $automations->filter(function ($automation){
            return in_array(Auth::user()->role_id,array_column(array_filter($automation->flow_array,function ($flow)use($automation){return $flow["priority"] == $automation->current_priority;}),"role_id"));
        });
    }
    #[ArrayShape(["result" => "string", "message" => "array"])] public function automate($direction, $comment): array
    {
        $result = ["result" => "","message" => []];
        $index = array_search($this->current_priority,array_column($this->flow_array,"priority"));
        $current_flow = $this->flow_array[$index];
        switch ($direction){
            case "forward":{
                if ($index >= 0){
                    if ($current_flow["is_main_role"]){
                        $this->update(["current_priority" => 0,"is_read" => 1,"is_finished" => 1,"editable" => 0]);
                        $this->automationable->update(["is_accepted" => 1,"is_refused" => 0]);
                        $result["result"] = "finished";
                    }
                    else{
                        $max_priority = max(array_values(array_column($this->flow_array,"priority")));
                        if ($current_flow["priority"] >= $max_priority)
                            $result["result"] = "no_main_role";
                        else {
                            $next_priority = $current_flow["priority"] + 1;
                            $next_index = array_search($next_priority, array_column($this->flow_array, "priority"));
                            if ($next_index >= 0) {
                                $this->update(["current_priority" => $next_priority, "current_role_id" => $this->flow_array[$next_index]["role_id"], "is_read" => 0, "editable" => 0]);
                                $result["result"] = "sent";
                            }
                            else
                                $result["result"] = "mismatch";

                        }
                    }
                    $this->signs()->updateOrCreate(["automation_id" => $this->id,"user_id" => Auth::id()],["sign" => Auth::user()->sign_hash,"refer" => 0]);
                    if ($comment)
                        $this->comments()->updateOrCreate(["automation_id" => $this->id,"user_id" => Auth::id()],["comment" => $comment]);
                }
                else
                    $result["result"] = "mismatch";
                break;
            }
            case "backward":{
                if ($index >= 0){
                    $min_priority = min(array_values(array_column($this->flow_array,"priority")));
                    if ($current_flow["priority"] < $min_priority)
                        $result["result"] = "mismatch";
                    elseif ($current_flow["priority"] == $min_priority){
                        $this->update(["current_priority" => null, "current_role_id" => null, "is_read" => 0, "editable" => 1]);
                        $result["result"] = "returned";
                    }
                    else{
                        $previous_priority = $current_flow["priority"] - 1;
                        $previous_index = array_search($previous_priority, array_column($this->flow_array, "priority"));
                        if ($previous_index >= 0) {
                            $this->update(["current_priority" => $previous_priority, "current_role_id" => $this->flow_array[$previous_index]["role_id"], "is_read" => 0, "editable" => 0]);
                            $result["result"] = "referred";
                        }
                        else
                            $result["result"] = "mismatch";

                    }
                    $this->signs()->updateOrCreate(["automation_id" => $this->id,"user_id" => Auth::id()],["sign" => Auth::user()->sign_hash,"refer" => 1]);
                    $this->signs()->where("refer","=",0)->delete();
                    if ($comment)
                        $this->comments()->updateOrCreate(["automation_id" => $this->id,"user_id" => Auth::id()],["comment" => $comment]);
                }
                else
                    $result["result"] = "mismatch";
                break;
            }
        }
        if ($result["result"] != "mismatch" && $result["result"] != "no_main_role") {
            $result["message"]["users"] = User::RecipientAutomation($this->current_role_id, $this->contract_id);
            $result["message"]["data"]["message"] = "رکورد جدید در اتوماسیون نامه های اداری دریافت شد";
            $result["message"]["data"]["id"] = $this->id;
            $result["message"]["data"]["type"] = "request";
            $result["message"]["data"]["action"] = route("EmployeeRequestsAutomation.index");
        }
        return $result;
    }
    public function GetMainUser(): Model|\Illuminate\Database\Eloquent\Relations\HasMany|array|null
    {
        $user = [];
        $flow_details = json_decode($this->flow,true);
        $main_role = array_filter($flow_details,function ($flow){
            return $flow["is_main_role"] == 1;
        });
        if ($main_role){
            $user = $this->signs()->whereHas("user", function ($query) use ($main_role){
                $query->whereIn("users.role_id",array_column($main_role,"role_id"));
            })->with("user.role")->first();
        }
        return $user;
    }
    public static function statistics($kind): array
    {
        $result = [];
        switch ($kind){
            case "MonthCounter":{
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
                        "count" => Automation::query()->whereDate("created_at",">=",$from_date)->whereDate("created_at","<=",$to_date)->count()
                    ];
                }
                break;
            }
            case "KindCounter":{
                User::UserType() == "admin" ?
                    $records = Automation::all()->where("is_finished","=",1)
                    :
                    $records = Automation::query()->whereHas("current_role",function ($query){
                        $query->where("roles.id","=",Auth::user()->role->id);
                    })->whereHas("contract",function ($query){
                        $query->whereIn("id",Contract::GetPermitted());
                    })->where("is_finished","=",1);
                $records->map(function ($record) use (&$result){
                    $i = 0;
                    $kind = match ($record->automationable_type){
                        PersonnelAppointmentForm::class => "حکم کارگزینی",
                        LoanPaymentConfirmationApplication::class => "کسر از حقوق",
                        EmploymentCertificateApplication::class => "اشتغال به کار",
                        OccupationalMedicineApplication::class => "طب کار",
                        BackgroundCheckApplication::class => "سوءپیشینه",
                        SettlementFormApplication::class => "تسویه حساب",
                    };
                    $result[][$kind] = [
                        "kind" => $kind,
                        "count" => ++$i
                    ];
                });
                break;
            }
        }
        return $result;
    }
    public function GetApplicationNameAttribute(): string
    {
        return match ($this->automationable_type){
            PersonnelAppointmentForm::class => "حکم کارگزینی",
            LoanPaymentConfirmationApplication::class => "نامه کسر از حقوق (به همراه ضمانت)",
            EmploymentCertificateApplication::class => "گواهی اشتغال به کار",
            OccupationalMedicineApplication::class => "طب کار",
            BackgroundCheckApplication::class => "عدم سوءپیشینه",
            SettlementFormApplication::class => "فرم تسویه حساب",
        };
    }
    public function GetApplicationClassAttribute(): string
    {
        return match ($this->automationable_type){
            PersonnelAppointmentForm::class => "PAF",
            LoanPaymentConfirmationApplication::class => "LPCA",
            EmploymentCertificateApplication::class => "ECA",
            OccupationalMedicineApplication::class => "OMA",
            BackgroundCheckApplication::class => "BCA",
            SettlementFormApplication::class => "SFA",
        };
    }
    public function GetExpirationDateAttribute(): string
    {
        try {
            $diff_days = verta($this->updated_at)->diffDays();
            if ($this->automationable->is_accepted == 1 && $diff_days < 15)
                return "remain";
            elseif ($this->automationable->is_accepted == 1 && $diff_days >= 15)
                return "expired";
            elseif ($this->automationable->is_refused == 1)
                return "refused";
            else
                return "waiting";

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function GetFlowArrayAttribute(){
        return json_decode($this->flow,true);
    }
}
