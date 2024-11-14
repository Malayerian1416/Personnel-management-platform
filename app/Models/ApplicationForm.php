<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use JetBrains\PhpStorm\ArrayShape;

class ApplicationForm extends Model
{
    use HasFactory;
    protected $table = "application_forms";
    protected $fillable = ["user_id","flow_id","name","application_form_type","related_id"];
    protected $appends = ["class_name","application_class"];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function form(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(FormTemplate::class,"application_form_id");
    }
    public static function Application($type): Model|\Illuminate\Database\Eloquent\Builder|null
    {
        return self::query()->where("application_form_type","=",$type)->with("form")->first();
    }
    public function automation_flow(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AutomationFlow::class,"flow_id");
    }
    public function GetClassNameAttribute(): string
    {
        return match ($this->application_form_type){
            "PersonnelAppointmentForm" => "حکم کارگزینی",
            "LoanPaymentConfirmationApplication" => "نامه کسر از حقوق",
            "EmploymentCertificateApplication" => "گواهی اشتغال به کار",
            "OccupationalMedicineApplication" => "طب کار",
            "BackgroundCheckApplication" => "عدم سوءپیشینه",
            "SettlementFormApplication" => "فرم تسویه حساب",
        };
    }
    public function GetApplicationClassAttribute(): string
    {
        return match ($this->application_form_type){
            "PersonnelAppointmentForm" => "PAF",
            "LoanPaymentConfirmationApplication" => "LPCA",
            "EmploymentCertificateApplication" => "ECA",
            "OccupationalMedicineApplication" => "OMA",
            "BackgroundCheckApplication" => "BCA",
            "SettlementFormApplication" => "SFA",
        };
    }
    #[ArrayShape(["current_role_id" => "\Illuminate\Database\Eloquent\HigherOrderBuilderProxy|int|mixed", "current_priority" => "int|mixed", "details" => "array|false|string"])] public static function MakeAutomation($class): array
    {
        $form = self::query()->with("automation_flow.details")->where("application_form_type","=",$class)->first();
        $flow = ["current_role_id" => 0,"current_priority" => 0,"details" => []];
        if ($form != null){
            if (User::UserType() =="staff") {
                $current = $form->automation_flow->details->where("role_id", "=", Auth::user()->role->id)->first();
                $min_priority = $current->priority;
            }
            else
                $min_priority = $form->automation_flow->details->min("priority");
            $flow["current_priority"] = $min_priority;
            $flow["current_role_id"] = $form->automation_flow->details->where("priority","=",$min_priority)->first()->role_id;
            foreach ($form->automation_flow->details as $detail)
                $flow["details"][] = ["role_id" => $detail->role_id,"priority" => $detail->priority,"is_main_role" => $detail->is_main_role];
        }
        $flow["details"] = json_encode($flow["details"]);
        return $flow;
    }
}
