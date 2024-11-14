<?php

namespace App\Http\Controllers;

use App\Events\NewNotification;
use App\Imports\ImportCustomGroupExcel;
use App\Models\Contract;
use App\Models\CustomGroup;
use App\Models\Organization;
use App\Models\SmsDelivery;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Hekmatinasser\Verta\Verta;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use JetBrains\PhpStorm\ArrayShape;
use nusoap_client;
use SoapClient;
use Throwable;
use ZipArchive;
use function Clue\StreamFilter\fun;
use function Psl\Regex\matches;
use function VeeWee\Xml\Xslt\Configurator\functions;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function activation($model): string
    {
        if ($model->inactive == 1)
            $model->update(["inactive" => 0]);
        else
            $model->update(["inactive" => 1]);
        return match($model->inactive){
            0 => "active",
            1 => "inactive",
            default => "unknown"
        };
    }
    public function download($folder, $disk, $folder_type): array
    {
        try {
            $zip = new ZipArchive();
            $name = Str::random(8);
            if (!Storage::disk($disk)->exists("/zip/{$folder}"))
                Storage::disk($disk)->makeDirectory("/zip/{$folder}");
            if ($zip->open(Storage::disk($disk)->path("/zip/{$folder}/{$name}.zip"), ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                $files = File::files(storage_path("/app/$folder_type/$disk/{$folder}"));
                foreach ($files as $file)
                    $zip->addFile($file, basename($file));
                $zip->close();
                return ["success" => 1,"message" => "ready","folder" => $folder,"name" => "$name.zip"];
            }
            else
                return ["success" => 0,"message" => "عدم توانایی در ساخت فایل فشرده"];
        }
        catch (Throwable $error){
            return ["success" => 0,"message" => $error->getMessage()];
        }
    }

    public function SendNotification($users,$data): void
    {
        if (count($users) > 0){
            foreach ($users as $user)
                event(new NewNotification($user->id,$data));
        }
    }

    public function persian_month($month = null): array|string
    {
        $months = ["فروردین","اردیبهشت","خرداد","تیر","مرداد","شهریور","مهر","آبان","آذر","دی","بهمن","اسفند"];
        if ($month)
            return $months[--$month];
        else
            return $months;
    }

    public function convert_numbers($string): string
    {
        return Str::replace([1,2,3,4,5,6,7,8,9,0],["۱", "۲","۳","۴","۵","۶","۷","۸","۹","۰"],$string);
    }

    public function send_sms(array $mobile_numbers,string $text): bool
    {
        try {
            $sms_client = new nusoap_client(env("SMS_WSDL_LINK"),"wsdl",false,false,false,false,10,20);
            $sms_client->soap_defencoding = 'UTF-8';
            $sms_client->decode_utf8 = FALSE;
            $result = $sms_client->call('sendSMS', array(
                'domain' => env("SMS_DOMAIN"),
                'username' => env("SMS_WSDL_USERNAME"),
                'password' => env("SMS_WSDL_PASSWORD"),
                'from' => env("SMS_FROM_NUMBER"),
                "to"=>implode(";",$mobile_numbers),
                "text"=>$text."\n\rهمیاران شمال شرق - لغو11",
                "isflash"=>1));
            SmsDelivery::query()->create([
                "sent_id" => $result,
                "delivery_result" => $sms_client->call('getDelivery', ['domain' => env("SMS_DOMAIN"),'username' => env("SMS_WSDL_USERNAME"),'password' => env("SMS_WSDL_PASSWORD"), 'id' => $result])
            ]);
            return true;
        }
        catch (Exception $error)
        {
            return false;
        }
    }

    public function sms_result_expression($results): string
    {
        $flag = 0;
        foreach ($results as $result){
            if($result["status"] <= 0)
                $flag = 1;
        }
        return $flag;
    }

    #[ArrayShape(["seconds" => "float|int", "limit" => "int"])] public function verify_remain_seconds($timestamp, $too_attempts = null): array
    {
        $limit = 120;
        if ($too_attempts)
            $limit = 300;
        return ["seconds" => Carbon::now()->diffInSeconds(Carbon::createFromFormat('Y-m-d H:i:s', $timestamp)),"limit" => $limit];
    }

    public function image_resize($file): \Intervention\Image\Image
    {
        $image = Image::make($file);
        $quality = 70;$width=1250;$height=950;$filename=Str::random(8).".jpg";
        if ($image->width() > $image->height())
            $height = null;
        else
            $width = null;
        if ($image->filesize() > 307200) {
            $image->save(Storage::path("tmp/{$filename}"), $quality, "jpg");
            while (Storage::Size("/tmp/{$filename}") > 307200) {
                Storage::Delete("/tmp/{$filename}");
                $image = Image::make($file);
                $image->resize($width, $height, function ($img) {
                    $img->aspectRatio();
                    $img->upsize();
                });
                $filename = Str::random(8).".jpg";
                $image->save(Storage::path("tmp/{$filename}"), $quality, "jpg");
                if ($quality > 40)
                    $quality -= 5;
                else {
                    if ($width != null and $width > 800)
                        $width -= 50;
                    elseif ($height != null && $height > 600)
                        $height -= 50;
                    else {
                        break;
                    }
                }
            }
        }
        $files = Storage::allFiles("tmp");
        Storage::Delete($files);
        return $image->encode("jpg",$quality);
    }
    function allowed_groups(): \Illuminate\Database\Eloquent\Collection|array
    {
        return CustomGroup::query()->whereHas("user",function ($query){$query->where("id","=",Auth::id());})->get();
    }
    function allowed_contracts($view = null): \Illuminate\Database\Eloquent\Collection|array
    {
        if(User::UserType() == "staff")
            $result = Organization::query()->with(["contracts" => function ($query) {
                $query->where("inactive","=",0)->whereIn("id", Auth::user()->contracts()->pluck("contract_id"))->orWhere("user_id", Auth::id())->with(["employees.contract.organization", "employees.user", "employees.registrant_user"]);}])->whereHas("contracts", function ($query) {$query->whereIn("id", Auth::user()->contracts()->pluck("contract_id"))->orWhere("user_id", Auth::id());})->where("inactive","=",0)->get();
        elseif (User::UserType() == "admin" || User::UserType() == "superuser")
            $result = Organization::query()->where("inactive","=",0)->with(["contracts.employees.contract.organization","contracts.employees.user","contracts.employees.registrant_user"])->whereHas("contracts",function($query){$query->where("contracts.inactive","=",0);})->get();
        else
            abort(403);
        if (isset($view) && $view == "tree"){
            $contracts = [];
            foreach ($result as $organization){
                if ($organization->contracts->isNotEmpty()){
                    $organization_object = ["id" => $organization->name, "label" => $organization->name, "children" => []];
                    foreach ($organization->contracts as $contract){
                        $contract_object = ["id" => $contract->id, "label" => "{$contract->name} ({$contract->number})"];
                        if ($contract->parent_id === null) {
                            if ($contract->children->isNotEmpty()) {
                                foreach ($contract->children as $child) {
                                    $child_object = ["id" => $child->id, "label" => $child->name];
                                    $contract_object["children"][] = $child_object;
                                }
                            }
                            $organization_object["children"][] = $contract_object;
                        }
                    }
                    $contracts[] = $organization_object;
                }
            }
            return $contracts;
        }
        else
            return $result;
    }
    public function Gregorian($date)
    {
        $date_sep = explode("/",$date);
        return implode("-",Verta::jalaliToGregorian(intval($date_sep[0]),intval($date_sep[1]),intval($date_sep[2])));
    }
}
