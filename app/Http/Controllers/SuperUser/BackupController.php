<?php

namespace App\Http\Controllers\SuperUser;

use App\Http\Controllers\Controller;
use App\Jobs\BackupInformation;
use App\Models\SystemBackup;
use App\Models\SystemConfigurationLog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class BackupController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            return view("superuser.backup",[
                "backups" => SystemBackup::query()->with("user")->get(),
            ]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical_error" => $error->getMessage()]);
        }
    }
    public function backup(Request $request): array
    {
        try {
            if (Storage::disk("backup_flag")->exists("current.txt"))
                Throw new Exception("فرایند دیگری در حال اجرا می باشد.لطفا تا پایان آن تامل فرمایید");
            $request->validate(["name" => "required","backup_type" => ["required",Rule::in(["Database","EmployeeDocs"])]],[
                "name.required" => "درج نام برای پشتیبان الزامی می باشد",
                "backup_type.required" => "انتخاب نوع پشتیبان الزامی می باشد",
                "backup_type.in" => "نوع پشتیبان انتخاب شده معتبر نمی باشد"
            ]);
            $type = $request->input("backup_type");
            $name = $request->input("name");
            DB::beginTransaction();
            $backup = SystemBackup::query()->create([
                "user_id" => Auth::id(),
                "name" => $name,
                "type" => $type,
                "status" => "r"
            ]);
            DB::commit();
            Storage::disk("backup_flag")->put("current.txt",$backup->id);
            $this->dispatch(new BackupInformation($type));
            return [
                "backups" => SystemBackup::query()->with("user")->get()->toArray(),
                "result" => "success",
                "message" => "فرایند ایجاد فایل پشتیبان با موفقیت آغاز شد"
            ];

        }
        catch (Throwable $error){
            DB::rollBack();
            Storage::disk("backup_flag")->delete("current.txt");
            return ["result" => "failed","message" => $error->getMessage()];
        }
    }
    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        try {
            $backup = SystemBackup::query()->findOrFail($id);
            if ($backup->download)
                Storage::disk("system_backups")->delete($backup->filename);
            $backup->delete();
            return redirect()->back()->with(["result" => "success","message" => "deleted"]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical_error" => $error->getMessage()]);
        }
    }
    public function stream(): array
    {
        return SystemBackup::query()->with("user")->get()->toArray();
    }
    public function backup_download($path): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
    {
        try {
            return response()->download(Storage::disk("system_backups")->path(Str::replace("@","/",$path)));
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical_error" => $error->getMessage()]);
        }
    }
}
