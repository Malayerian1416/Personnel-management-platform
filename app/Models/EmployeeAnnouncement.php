<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAnnouncement extends Model
{
    use HasFactory;
    protected $table = "employee_announcements";
    protected $fillable = ["user_id","employee_id","title","comment","needed_information","make_decision","decision","compilation_date"];
    protected $appends = ["files","texts"];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class,"employee_id");
    }
    public function GetFilesAttribute(){
        return isset(json_decode($this->needed_information,true)["files"]) ? json_decode($this->data_items,true)["files"] : [];
    }
    public function GetTextsAttribute(){
        return isset(json_decode($this->needed_information,true)["texts"]) ? json_decode($this->data_items,true)["texts"] : [];
    }
}
