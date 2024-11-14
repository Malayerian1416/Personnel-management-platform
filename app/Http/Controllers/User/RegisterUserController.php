<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\FollowUpRegistration;
use App\Http\Requests\StepFiveRequest;
use App\Http\Requests\StepFourRequest;
use App\Http\Requests\StepOneRequest;
use App\Http\Requests\StepSixRequest;

use App\Models\Bank;
use App\Models\ContractPreEmployee;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\Province;
use App\Models\ReloadEmployeeData;
use App\Models\UnregisteredEmployee;
use App\Rules\NationalCodeChecker;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Throwable;

class RegisterUserController extends Controller
{
    public function introduction(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view("user.registration.introduction");
    }

    public function follow_up_registration(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $national_code = $request->input("national_code");
        return view("user.registration.follow_up",["national_code" => $national_code]);
    }

    public function following_up_registration(FollowUpRegistration $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        try {
            $validated = $request->validated();
            $employee = ContractPreEmployee::query()->where("national_code","=",$validated["national_code"])
                ->where("tracking_code","=",$validated["tracking_code"])->first();
            $reload_data = $employee->reload_data()->where("is_loaded","=",0)->first();
            if ($reload_data != null)
                return redirect()->route("registration_reload_data_index",["id" => $reload_data->id]);
            if ($employee && $employee->registered)
                return redirect()->back()->with(["follow_up_message" => "همکار گرامی ، " . $employee->name . " ؛ اطلاعات ثبت نام شما توسط کارشناس سازمان در دست بررسی می باشد و نتیجه آن از طریق پیامک به شما اطلاع رسانی خواهد شد."]);
            elseif (Employee::employee($validated["national_code"]))
                return redirect()->back()->with(["follow_up_message" => "همکار گرامی؛ ثبت نام شما توسط کارشناس سازمان تایید شده است. لطفا جهت ورود به سامانه با استفاده از کد ملی به عنوان نام کاربری و شماره موبایل به عنوان گذرواژه استفاده نمایید."]);
            else
                return redirect()->back()->with(["follow_up_message" => "اطلاعات شما در سامانه یافت نشد"]);

        }
        catch (Throwable $error){
            return redirect()->route('step_one')->withErrors(["logical_error" => $error->getMessage()]);
        }
    }

    public function step_one(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        Session::put("register.steps",5);
        Session::put("register.current_step",1);
        return view("user.registration.step_one",["organizations" => Organization::all()]);
    }
    public function step_two(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        Session::put("register.current_step",2);
        $employee = ContractPreEmployee::employee(Session::get("register.national_code"));
        return view("user.registration.step_two",["employee_name" => $employee->name]);
    }
    public function step_three(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        Session::put("register.current_step",2);
        $employee = ContractPreEmployee::employee(Session::get("register.national_code"));
        $remain_time = $this->verify_remain_seconds($employee->verify_timestamp, Session::get("register.sms_attempts") >= 3);
        return view("user.registration.step_three",["remain_time" => $remain_time]);
    }

    public function step_four(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        Session::put("register.current_step",3);
        $provinces = Province::query()->with("cities")->get();
        return view("user.registration.step_four",[
            "provinces" => $provinces,
            "olds" => Session::exists("register.personal_information") ? json_decode(Session::get("register.personal_information"),true) : []
        ]);
    }

    public function step_five(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        Session::put("register.current_step",4);
        $employee = ContractPreEmployee::employee(Session::get("register.national_code"));
        return view("user.registration.step_five",[
            "organization" => $employee->contract->organization->name,
            "banks" => Bank::all(),
            "olds" => Session::exists("register.job_information") ? json_decode(Session::get("register.job_information"),true) : []
        ]);
    }

    public function step_six($clear = null): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        Session::put("register.current_step", 5);
        $docs = [];
        if ($clear)
            Storage::disk("employee_docs")->deleteDirectory(Session::get("register.national_code"));
        if (count(Storage::disk("employee_docs")->allFiles(Session::get("register.national_code"))) > 0) {
            foreach (Storage::disk("employee_docs")->allFiles(Session::get("register.national_code")) as $file)
                $docs [] = base64_encode(Storage::disk("employee_docs")->get($file));
        }
        return view("user.registration.step_six",["docs" => $docs]);
    }
    public function check_up(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $data = array_merge(json_decode(Session::get("register.personal_information"),true),json_decode(Session::get("register.job_information"),true));
        $docs = [];
        foreach (Storage::disk("employee_docs")->allFiles(Session::get("register.national_code")) as $file)
            $docs [] = base64_encode(Storage::disk("employee_docs")->get($file));
        return view("user.registration.final_approval",["data" => $data,"docs" => $docs]);
    }
    public function success_registration(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        if (Session::exists("register.tracking_code"))
            return view("user.registration.success");
        else
            abort(404);
    }

    public function check_national_code(StepOneRequest $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $validated = $request->validated();
            $employee = ContractPreEmployee::employee($validated["national_code"]);
            if (Employee::employee($validated["national_code"]))
                return redirect()->route("step_one")->withErrors(['not_found' => 'ثبت نام شما قبلا در سامانه انجام شده است']);
            elseif ($employee != null) {
                if ($employee->registered == 1)
                    return to_route("follow_up_registration",["national_code" => $validated["national_code"]]);
                if (!Session::exists("register.national_code")){
                    Session::put("register.national_code",$employee->national_code);
                    Session::put("register.sms_attempts",0);
                }
                if (Session::exists("register.national_code") && Session::get("register.national_code") != $validated["national_code"])
                    Session::put("register.national_code",$employee->national_code);
                return redirect()->route("step_two");
            }
            else
                return redirect()->route("step_one")->withErrors(['not_found' => 'کد ملی وارد شده در سامانه ثبت نام موجود نمی باشد']);
        }
        catch (Throwable $error){
            return redirect()->route('step_one')->withErrors(["logical_error" => $error->getMessage()]);
        }
    }

    public function send_verification_code(Request $request): \Illuminate\Http\RedirectResponse
    {
        try{
            DB::beginTransaction();
            $request->validate(["mobile" => ["required", "regex:/^09(1[0-9]|9[0-2]|2[0-2]|0[1-5]|41|3[0,3,5-9])\d{7}$/"]],
                ["mobile.required" => "درج شماره تلفن همراه الزامی می باشد","mobile.regex" => "فرمت شماره تلفن همراه صحیح نمی باشد"]);
            $duplicate = Employee::query()->where("mobile","=",$request->mobile)->get();
            if ($duplicate->isNotEmpty())
                return redirect()->route("step_two")->withErrors(['duplicated' => 'شماره تلفن همراه وارد شده قبلا در سامانه ثبت شده و تکراری می باشد']);
            else {
                $employee = ContractPreEmployee::employee(Session::get("register.national_code"));
                $employee->update(["mobile" => $request->mobile]);
                if ($employee->verify_timestamp == null || $this->verify_remain_seconds($employee->verify_timestamp)["seconds"] > $this->verify_remain_seconds($employee->verify_timestamp)['limit']) {
                    $verify_code = 12345;//rand(13254, 99898);
                    $text = env("SMS_ACTIVATION_CODE_TEXT") . "\n\r{$verify_code}";
                    $sms_result = $this->send_sms([$employee->mobile], $text);
                    if ($sms_result) {
                        Session::put("register.mobile", $employee->mobile);
                        Session::put("register.sms_attempts", Session::get("register.sms_attempts") + 1);
                        $employee->update(["verify" => Hash::make($verify_code), "verify_timestamp" => date("Y-m-d H:i:s")]);
                        DB::commit();
                        return redirect()->route("step_three");
                    } else
                        return redirect()->back()->withErrors(['sms_fail' => 'در حال حاضر امکان ارسال پیامک وجود ندارد. لطفا چند لحظه بعد مجددا اقدام فرمایید']);
                } else
                    return redirect()->route("step_three");
            }
        }
        catch(Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical_error" => $error->getMessage()]);
        }
    }

    public function resend_verification_code(): \Illuminate\Http\RedirectResponse
    {
        try {
            $employee = ContractPreEmployee::employee(Session::get("register.national_code"));
            if ($employee->verify_timestamp == null || $this->verify_remain_seconds($employee->verify_timestamp)["seconds"] > $this->verify_remain_seconds($employee->verify_timestamp)['limit']) {
                $verify_code = 12345;//rand(13254, 99898);
                $text = env("SMS_ACTIVATION_CODE_TEXT") . "\n\r{$verify_code}";
                $sms_result = $this->send_sms([$employee->mobile], $text);
                if ($sms_result) {
                    Session::put("register.sms_attempts", Session::get("register.sms_attempts") + 1);
                    $employee->update(["verify" => Hash::make($verify_code), "verify_timestamp" => date("Y-m-d H:i:s")]);
                    return redirect()->route("step_three");
                } else
                    return redirect()->back()->withErrors(['sms_fail' => 'در حال حاضر امکان ارسال پیامک وجود ندارد']);
            }
            return redirect()->route("step_three")->withErrors(["sent" => "کد فعال سازی قبلا برای شما ارسال شده است"]);
        }
        catch (Throwable $error){
            return redirect()->route('step_three')->withErrors(["logical_error" => $error->getMessage()]);
        }
    }

    public function check_verification_code(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $request->validate(["activation_code.*" => ["required", "digits_between:0,9"]],
                ["activation_code.required" => "لطفا کد فعال سازی را وارد نمایید","activation_code.digits_between" => "فرمت کد فعال سازی صحیح نمی باشد"]);
            $employee = ContractPreEmployee::employee(Session::get("register.national_code"));
            if (Hash::check(implode("",array_reverse($request->input("activation_code"))),$employee->verify)) {
                Session::put("register.activation",true);
                return redirect()->route("step_four");
            }
            else
                return redirect()->back()->withErrors(['activation_code' => 'کد فعال سازی وارد شده صحیح نمی باشد']);
        }
        catch (Throwable $error){
            return redirect()->route('step_three')->withErrors(["logical_error" => $error->getMessage()]);
        }
    }

    public function store_personal_information(StepFourRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $validated = $request->validated();
            $pre_employee = ContractPreEmployee::employee(Session::get("register.national_code"));
            $validated["national_code"] = $pre_employee["national_code"];
            $validated["mobile"] = $pre_employee["mobile"];
            $validated["contract_id"] = $pre_employee["contract_id"];
            Session::put("register.gender",$validated["gender"]);
            Session::put("register.personal_information",json_encode($validated,JSON_UNESCAPED_UNICODE));
            return redirect()->route("step_five");
        }
        catch (Throwable $error){
            return redirect()->route('step_four')->withErrors(["logical_error" => $error->getMessage()]);
        }
    }

    public function store_job_information(StepFiveRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $validated = $request->validated();
            $pre_employee = ContractPreEmployee::employee(Session::get("register.national_code"));
            $validated["contract_subset_id"] = $pre_employee["contract_subset_id"];
            Session::put("register.insurance",$validated["insurance_days"]);
            Session::put("register.job_information", json_encode($validated, JSON_UNESCAPED_UNICODE));
            return redirect()->route("step_six");
        }
        catch (Throwable $error) {
            return redirect()->route('step_five')->withErrors(["logical_error" => $error->getMessage()]);
        }
    }

    public function store_image_documents(StepSixRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $national_code = Session::get("register.national_code");
            Storage::disk("employee_docs")->deleteDirectory("$national_code");
            if ($request->hasFile("birth_certificate")){
                foreach ($request->file("birth_certificate") as $file){
                    $filename = Str::random(8).".jpg";
                    $image = $this->image_resize($file);
                    Storage::disk("employee_docs")->put("/{$national_code}/birth_certificate/{$filename}",$image);
                }
            }
            if ($request->hasFile("national_card")){
                $filename = Str::random(8).".jpg";
                $image = $this->image_resize($request->file("national_card"));
                Storage::disk("employee_docs")->put("/{$national_code}/national_card/{$filename}",$image);
            }
            if ($request->hasFile("military_certificate")){
                $filename = Str::random(8).".jpg";
                $image = $this->image_resize($request->file("military_certificate"));
                Storage::disk("employee_docs")->put("/{$national_code}/military_certificate/{$filename}",$image);
            }
            if ($request->hasFile("education_certificate")){
                $filename = Str::random(8).".jpg";
                $image = $this->image_resize($request->file("education_certificate"));
                Storage::disk("employee_docs")->put("/{$national_code}/education_certificate/{$filename}",$image);
            }
            if ($request->hasFile("personal_photo")){
                $filename = Str::random(8).".jpg";
                $image = $this->image_resize($request->file("personal_photo"));
                Storage::disk("employee_docs")->put("/{$national_code}/personal_photo/{$filename}", (string) $image);
            }
            if ($request->hasFile("insurance_confirmation")){
                $filename = Str::random(8).".jpg";
                $image = $this->image_resize($request->file("insurance_confirmation"));
                Storage::disk("employee_docs")->put("/{$national_code}/insurance_confirmation/{$filename}",$image);
            }
            Session::put("register.documents",true);
            return redirect()->route("check_up");
        }
        catch (Throwable $error) {
            DB::rollBack();
            Storage::disk("employee_docs")->deleteDirectory(Session::get("register.national_code"));
            return redirect()->route('step_six')->withErrors(["logical_error" => $error->getMessage()]);
        }
    }
    public function register_employee(): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $national_code = Session::get("register.national_code");
            $pre_employee = ContractPreEmployee::employee("$national_code");
            $data = array_merge(json_decode(Session::get("register.personal_information"), true), json_decode(Session::get("register.job_information"), true));
            $employee = Employee::query()->create($data);
            $tracking_code = rand(1056854, 9842881);
            while (ContractPreEmployee::query()->where("tracking_code", "=", $tracking_code)->exists())
                $tracking_code = rand(1056854, 9842881);
            $pre_employee->update([
                "tracking_code" => $tracking_code,
                "registered" => 1,
                "registration_date" => Carbon::now()->format("Y/m/d H:i:s")
            ]);
            $text = env("SMS_REGISTER_TRACKING_CODE") . "\n\r{$tracking_code}";
            $sms_result = $this->send_sms([$employee->mobile], $text);
            if ($sms_result) {
                DB::commit();
                $notifications = $pre_employee->RegisteredMessaging();
                $this->SendNotification($notifications["message"]["users"],$notifications["message"]["data"]);
                Session::put("register.tracking_code", $tracking_code);
                Session::forget([
                    "register.national_code",
                    "register.steps",
                    "register.current_step",
                    "register.personal_information",
                    "register.job_information",
                    "register.gender",
                    "register.sms_attempts",
                    "register.insurance",
                    "register.mobile",
                    "register.activation"
                ]);
                return redirect()->route("success_registration", ["national_code" => $employee->national_code]);
            } else {
                Storage::disk("employee_docs")->deleteDirectory($employee->national_code);
                return redirect()->back()->withErrors(['sms_fail' => 'در حال حاضر امکان ارسال پیامک وجود ندارد']);
            }
        }
        catch (Throwable $error) {
            DB::rollBack();
            return redirect()->back()->withErrors(["logical_error" => $error->getMessage()]);
        }
    }
    public function unregistered_employees(Request $request): array
    {
        try {
            DB::beginTransaction();
            $request->validate([
                "name" => "required",
                "national_code" => ["required",new NationalCodeChecker()],
                "organization" => "required",
                "mobile" => "required",
                "description" => "sometimes|nullable"
            ],[
                "name.required" => "درج نام الزامی می باشد",
                "national_code.required" => "درج کد ملی الزامی می باشد",
                "organization.required" => "انتخاب سازمان مربوطه الزامی می باشد",
                "mobile.required" => "درج شماره تلفن همراه الزامی می باشد",
            ]);
            $duplicate = UnregisteredEmployee::query()->where("national_code","=",$request->input("national_code"))->get();
            $registered = Employee::query()->where("national_code","=",$request->input("national_code"))->get();
            $registration = ContractPreEmployee::query()->where("national_code","=",$request->input("national_code"))->get();
            if ($duplicate->isNotEmpty())
                $response["message"] = "اطلاعات شما قبلا ثبت گردیده است";
            elseif ($registered->isNotEmpty())
                $response["message"] = "ثبت نام شما قبلا در سامانه انجام شده است";
            elseif ($registration->isNotEmpty())
                $response["message"] = "کد ملی شما در سامانه ثبت نام وجود دارد";
            else{
                UnregisteredEmployee::query()->create([
                    "name" => $request->input("name"),
                    "national_code" => $request->input("national_code"),
                    "organization_id" => $request->input("organization"),
                    "mobile" => $request->input("mobile"),
                    "description" => $request->input("description")
                ]);
                DB::commit();
                $notifications = ContractPreEmployee::UnRegisteredMessaging($request->input("organization"));
                $this->SendNotification($notifications["message"]["users"],$notifications["message"]["data"]);
                $response["message"] = "اطلاعات شما با موفقیت ذخیره و ارسال شد";
            }
            return $response;
        }
        catch (Throwable $error){
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage().$error->getLine();
            return $response;
        }
    }
    public function reload_data_index($id): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|array|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $reload_data = ReloadEmployeeData::query()->findOrFail($id);
            return view("user.registration.reload_data", ["reload_data" => $reload_data]);
        }
        catch (Throwable $error){
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = "متاسفانه در حال حاضر امکان ثبت اطلاعات وجود ندارد!";
            return $response;
        }
    }
    public function reload_data(Request $request,$id): \Illuminate\Http\RedirectResponse
    {
        $reload_data = ReloadEmployeeData::query()->with("reloadable")->findOrFail($id);
        $rules = [];
        $messages = [];
        foreach ($reload_data->docs as $doc) {
            $rules[$doc["data"]] = "required";
            $rules["{$doc["data"]}.*"] = "mimes:jpg,jpeg,png,svg,gif,tiff,bmp|max:2048";
            $messages["{$doc['data']}.required"] = "مقدار این فیلد را وارد نمایید";
            $messages["{$doc['data']}.*.mimes"] = "یک یا چند فایل تصویر بارگذاری شده دارای فرمت صحیح نمی باشد";
            $messages["{$doc['data']}.*.max"] = "حداکثر حجم مجاز برای هر فایل، 2 مگابایت می باشد";
        }
        foreach ($reload_data->databases as $database) {
            $rules[$database["data"]] = "required";
            $messages["{$database['data']}.required"] = "مقدار این فیلد را وارد نمایید";
        }
        $request->validate($rules, $messages);
        try {
            if ($reload_data->is_loaded == 1)
                return redirect()->back()->withErrors(["loaded" => "true"]);
            $employee = Employee::employee($reload_data->national_code);
            foreach ($reload_data->docs as $doc) {
                if($request->hasFile($doc["data"])) {
                    Storage::disk("employee_docs")->deleteDirectory("/{$reload_data->reloadable->national_code}/{$doc["data"]}");
                    foreach ($request->file($doc["data"]) as $file) {
                        $filename = Str::random(8) . ".jpg";
                        $image = $this->image_resize($file);
                        Storage::disk("employee_docs")->put("/{$reload_data->reloadable->national_code}/{$doc["data"]}/{$filename}", $image);
                    }
                }
            }
            foreach ($reload_data->databases as $database) {
                $input = $request->has($database["data"]) ? $request->input($database["data"]) : null;
                if ($input)
                    $employee->update([$database["data"] => $input]);
            }
            $reload_data->update(["is_loaded" => 1]);
            $reload_data->reloadable->update(["reload_date" => date("Y/m/d H:i:s")]);
            DB::commit();
            $notifications = ContractPreEmployee::employee($employee->national_code)->ReloadMessaging();
            $this->SendNotification($notifications["message"]["users"],$notifications["message"]["data"]);
            return redirect()->route("reload_data_finish");
        }
        catch (Throwable $error) {
            DB::rollBack();
            return redirect()->back()->withErrors(["logical_error" => $error->getMessage()]);
        }
    }
    public function reload_data_finish(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view("user.registration.reload_success");
    }
}
