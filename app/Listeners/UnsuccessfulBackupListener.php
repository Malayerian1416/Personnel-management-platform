<?php

namespace App\Listeners;

use App\Models\SystemBackup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Backup\Events\BackupHasFailed;
use Throwable;

class UnsuccessfulBackupListener
{
    public function handle(BackupHasFailed $event): void
    {
        try {
            $id = Storage::disk("backup_flag")->get("current.txt");
            SystemBackup::query()->find(intval($id))->update(["status" => "f","exception" => $event->exception->getMessage()]);
            Storage::disk("backup_flag")->delete("current.txt");
        }
        catch (Throwable $err){
            Storage::disk("backup_flag")->delete("current.txt");
            Log::debug($err->getMessage());
        }
    }
}
