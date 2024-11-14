<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeFinancialAdvantage extends Model
{
    use HasFactory;
    protected $table = "employee_financial_advantages";
    protected $fillable = ["user_id","employee_id","daily_wage","prior_service","working_days","occupational_group","count_of_children","effective_year","advantages"];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class,"employee_id");
    }
    public static function BatchAdvantages($contract_id,$effective_year): \Illuminate\Database\Eloquent\Collection|array
    {
        return self::query()->with(["user","employee.contract.organization"])->whereHas("employee",function ($query) use($contract_id){
            $query->where("employees.contract_id","=",$contract_id);
        })->where("effective_year","=",$effective_year)->get();
    }
}
