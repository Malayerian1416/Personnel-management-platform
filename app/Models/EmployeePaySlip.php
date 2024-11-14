<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;
use function Psl\Type\null;

class EmployeePaySlip extends Model
{
    use HasFactory;
    protected $table = "employee_payslips";
    protected $fillable = ["user_id","employee_id","i_number","date_serial","persian_year","persian_month","persian_month_name","contents"];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class,"employee_id");
    }
    public static function BatchPayslip($contract_id,$year,$month): \Illuminate\Database\Eloquent\Collection|array
    {
        return self::query()->with(["employee.contract.organization","user"])->whereHas("employee",function ($query) use($contract_id){
            $query->where("employees.contract_id","=",$contract_id);
        })->where("persian_year","=",$year)->where("persian_month","=",$month)->get();
    }
    public static function BatchPayslipDelete($contract_id,$year,$month)
    {
        return self::query()->with(["employee.contract.organization","user"])->whereHas("employee",function ($query) use($contract_id){
            $query->where("employees.contract_id","=",$contract_id);
        })->where("persian_year","=",$year)->where("persian_month","=",$month)->delete();
    }
    public function convert_numbers($string): string
    {
        return Str::replace([1,2,3,4,5,6,7,8,9,0],["۱", "۲","۳","۴","۵","۶","۷","۸","۹","۰"],$string);
    }
    public function published(): array
    {
        $result = [];
        $result["total_advantages"] = 0;$result["total_deductions"] = 0;
        $result["employee"] = [
            "name" => $this->employee->name,
            "national_code" => $this->convert_numbers($this->employee->national_code),
            "contract" => "{$this->employee->contract->organization->name}({$this->employee->contract->name})",
            "i_number" => $this->convert_numbers($this->i_number),
            "year" => $this->convert_numbers($this->persian_year),
            "month" => $this->persian_month_name,
            "report_date" => $this->convert_numbers(verta()->format("Y/m/d"))
        ];
        $qrcode_string = route("Validation.direct",["i_number" => $this->i_number,"type" => "payslip"]);
        $qrCode = new QrCode("$qrcode_string");
        $output = new Output\Png();
        $result["employee"]["qrcode"] = base64_encode($output->output($qrCode, 100, [255, 255, 255], [0, 0, 0]));
        $contents = json_decode($this->contents,true);
        $functions = array_filter($contents,function ($content){
            return $content["type"] == "function";
        });
        $advantages = array_filter($contents,function ($content){
            return $content["type"] == "advantage";
        });
        $deductions = array_filter($contents,function ($content){
            return $content["type"] == "deduction";
        });
        $result["functions"] = array_values(array_filter($functions,function ($func){return $func["value"] > 0;}));
        $result["advantages"] = array_values(array_filter($advantages,function ($adv){return $adv["value"] > 0;}));
        $result["deductions"] = array_values(array_filter($deductions,function ($ded){return $ded["value"] > 0;}));
        foreach ($functions as $function)
            $result["shuffle"][] = $function;
        foreach ($advantages as $advantage)
            $result["total_advantages"] += floatval($advantage["value"]);
        foreach ($deductions as $deduction)
            $result["total_deductions"] += floatval($deduction["value"]);
        $result["total_net"] = $this->convert_numbers(number_format($result["total_advantages"] - $result["total_deductions"]));
        $result["total_advantages"] = $this->convert_numbers(number_format($result["total_advantages"]));
        $result["total_deductions"] = $this->convert_numbers(number_format($result["total_deductions"]));
        for($i = 0 ; $i < count($result["functions"]) ; $i++)
            $result["functions"][$i]["value"] = $this->convert_numbers(number_format($result["functions"][$i]["value"]));
        for($j = 0 ; $j < count($result["advantages"]) ; $j++)
            $result["advantages"][$j]["value"] = $this->convert_numbers(number_format($result["advantages"][$j]["value"]));
        for($k = 0 ; $k < count($result["deductions"]) ; $k++)
            $result["deductions"][$k]["value"] = $this->convert_numbers(number_format(floatval($result["deductions"][$k]["value"])));
        return $result;
    }
    public static function Last($employee_id): array
    {
        $result = [];
        $result["total_advantages"] = 0;$result["total_deductions"] = 0;$result["total_net"] = 0;$result["year_month"] = "";$result["functions"] = [];$result["advantages"] = [];$result["deductions"] = [];
        $max_date = self::query()->where("employee_id","=",$employee_id)->max("date_serial");
        if ($max_date){
            $last_payslip = self::query()->where("date_serial","=",$max_date)->where("employee_id","=",$employee_id)->first();
            if ($last_payslip != null) {
                $result["year_month"] = $last_payslip->persian_month_name . " ماه سال " . $last_payslip->persian_year;
                $contents = json_decode($last_payslip->contents, true);
                $functions = array_filter($contents, function ($content) {
                    return $content["type"] == "function";
                });
                $advantages = array_filter($contents, function ($content) {
                    return $content["type"] == "advantage";
                });
                $deductions = array_filter($contents, function ($content) {
                    return $content["type"] == "deduction";
                });
                $result["functions"] = array_values($functions);
                $result["advantages"] = array_values($advantages);
                $result["deductions"] = array_values($deductions);
                foreach ($advantages as $advantage)
                    $result["total_advantages"] += floatval($advantage["value"]);
                foreach ($deductions as $deduction)
                    $result["total_deductions"] += floatval($deduction["value"]);
                $result["total_net"] = $result["total_advantages"] - $result["total_deductions"];
            }
        }
        else{
            $employee = Employee::query()->findOrFail($employee_id);
            $year = verta($employee->active_contract_date()["start"])->format("Y");
            $labour_law = LabourLawTariff::query()->where("effective_year","=",$year)->first();
            if ($labour_law != null)
                $result["total_net"] = $result["total_advantages"] = (30 * $labour_law->daily_wage) + $labour_law->household_consumables_allowance + $labour_law->housing_purchase_allowance + $labour_law->marital_allowance + ($employee->included_children_count * $labour_law->child_allowance);
            else
                $result["total_net"] = $result["total_advantages"] = 0;
        }
        return $result;
    }
}
