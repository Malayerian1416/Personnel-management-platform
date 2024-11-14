<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use function Clue\StreamFilter\fun;
use function VeeWee\Xml\Xslt\Configurator\functions;

class FormTemplate extends Model
{
    use HasFactory;use softDeletes;
    protected $table = "form_templates";
    protected $fillable = ["user_id","application_form_id","name","page_data","background","deleted_at"];

    public function application(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ApplicationForm::class,"application_form_id");
    }
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function computed($employee,$application = null){
        $keywords = DbKeyword::all()->toArray();
        $page_data = json_decode($this->page_data,true);
        if ($page_data["contents"]){
            for ($i = 0; $i < count($page_data["contents"]); $i++) {
                $string = $page_data["contents"][$i]["text"];
                $conversions = [];
                $matches = collect(Str::matchAll("/\/\/[\x{0621}-\x{0651}\x{067E}\x{0686}\x{0698}\x{06A9}\x{06AF}\x{06C0}\x{06CC}\s]+\/\//u",$string));
                $matches->map(function ($match) use (&$string,$keywords,$employee,&$conversions,$application){
                    array_map(function ($keyword) use ($match,$employee,&$conversions,$application){
                        if($keyword["tag"] == str_replace(" ","_",$match)){
                            switch ($keyword["resource"]) {
                                case "Employee":{
                                    $conversions[] = $keyword["style"] == "bold" ? "<b>{$employee[$keyword["data"]]}</b>" : "{$employee[$keyword["data"]]}";
                                    break;
                                }
                                case "ContractExtension":{
                                    $start = verta($employee->active_contract_date()['start'])->format("Y/m/d");
                                    $end = verta($employee->active_contract_date()['end'])->format("Y/m/d");
                                    if ($keyword["tag"] == "//تاریخ_شروع_قرارداد_جاری//")
                                        $conversions[] = $keyword["style"] == "bold" ? "<b>$start</b>" : "$start";
                                    if ($keyword["tag"] == "//تاریخ_پایان_قرارداد_جاری//")
                                        $conversions[] = $keyword["style"] == "bold" ? "<b>$end</b>" : "$end";
                                    break;
                                }
                                case "Application":{
                                    $class = get_class($application);
                                    if ($keyword["tag"] == "//شماره_نامه//")
                                        $conversions[] = $keyword["style"] == "bold" ? "<b>$application->i_number</b>" : "$application->i_number";
                                    if ($keyword["tag"] == "//گیرنده_نامه//")
                                        $conversions[] = $keyword["style"] == "bold" ? "<b>$application->recipient</b>" : "$application->recipient";
                                    if ($keyword["tag"] == "//موضوع_نامه//")
                                        $conversions[] = $keyword["style"] == "bold" ? "<b>$class</b>" : "$class";
                                    if ($keyword["tag"] == "//وام_گیرنده//")
                                        $conversions[] = $keyword["style"] == "bold" ? "<b>$application->borrower</b>" : "$application->borrower";
                                    break;
                                }
                                case "EmployeePayslip":{
                                    $payslip = EmployeePaySlip::Last($employee->id);
                                    $total = number_format($payslip['advantages']);
                                    $net = number_format($payslip['total_net']);
                                    if ($keyword["tag"] == "//آخرین_جمع_ناخالص_پرداختی//")
                                        $conversions[] = $keyword["style"] == "bold" ? "<b>$total</b>" : "$total";
                                    if ($keyword["tag"] == "//آخرین_جمع_خالص_پرداختی//")
                                        $conversions[] = $keyword["style"] == "bold" ? "<b>$net</b>" : "$net";
                                    break;
                                }
                                case "Direct":{
                                    if ($keyword["tag"] == "//تاریخ_روز//")
                                        $conversions[] = verta($application->updated_at)->format("Y/m/d");
                                    break;
                                }
                            }
                        }
                    }, $keywords);
                });
                if ($conversions){
                    $string = Str::replace($matches,$conversions,$string);
                }
                $page_data["contents"][$i]["text"] = $this->convert_numbers($string);
            }
            if ($this->background && storage::disk("form_templates")->exists("{$this->id}/{$this->background}"))
                $page_data["background"] = base64_encode(Storage::disk("form_templates")->get("{$this->id}/{$this->background}"));
            else
                $page_data["background"] = null;
            $page_data["orientation"] = match ($page_data["orientation"]){
                "portrait" => "P",
                "landscape" => "L",
                default => ""
            };
            return $page_data;
        }
        return null;
    }
    public function convert_numbers($string): string
    {
        return Str::replace([1,2,3,4,5,6,7,8,9,0],["۱", "۲","۳","۴","۵","۶","۷","۸","۹","۰"],$string);
    }
}
