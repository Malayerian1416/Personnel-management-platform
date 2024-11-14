<?php

namespace App\Listeners;

use App\Models\SystemBackup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Backup\Events\BackupWasSuccessful;
use Throwable;

class SuccessfulBackupListener
{

    public function handle(BackupWasSuccessful $event): void
    {
        try {
            $id = Storage::disk("backup_flag")->get("current.txt");
            SystemBackup::query()->find(intval($id))->update(["status" => "s","filename" => $event->backupDestination->newestBackup()->path()]);
            Storage::disk("backup_flag")->delete("current.txt");
        }
        catch (Throwable $err){
            Storage::disk("backup_flag")->delete("current.txt");
            Log::debug($err->getMessage());
        }
    }
}
