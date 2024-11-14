<?php

namespace App\Jobs;

use App\Models\SystemBackup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class BackupInformation implements ShouldQueue,ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $type;
    public mixed $id;

    public function __construct($type)
    {
        $this->type = $type;
        $this->id = Storage::disk("backup_flag")->exists("current.txt") ? intval(Storage::disk("backup_flag")->get("current.txt")) : 0;
    }

    public function handle(): void
    {
        try {
            match ($this->type) {
                "Database" => Artisan::call("backup:run --only-db"),
                "EmployeeDocs" => Artisan::call("backup:run --only-files")
            };
        }
        catch (Throwable $error){
            Storage::disk("backup_flag")->delete("current.txt");
            Log::debug($error->getMessage());
        }
    }
    public function failed($exception = null): void
    {
        try {
            if ($this->id > 0)
                SystemBackup::query()->find($this->id)->update(["status" => "f","exception" => $exception->getMessage()]);
            Storage::disk("backup_flag")->delete("current.txt");
        }
        catch (Throwable $error){
            Log::debug($error->getMessage());
        }
    }
}
