<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDataRequest extends Model
{
    use HasFactory;
    protected $table = "employee_data_requests";
    protected $fillable = ["user_id","employee_id","title","data_items","lock_dashboard","is_loaded","reload_date"];
    protected $appends = ["docs","databases"];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class,"employee_id");
    }
    public function GetDocsAttribute(){
        $result = [];
        $docs = isset(json_decode($this->data_items,true)["files"]) ? json_decode($this->data_items,true)["files"] : [];
        $keywords = DbKeyword::all();
        foreach ($docs as $doc) {
            $keyword = $keywords->where("data", "=", $doc)->first();
            if ($keyword)
                $result[] = ["name" => $keyword->name,"data" => $doc, "type" => "file"];
        }
        return $result;
    }
    public function GetDatabasesAttribute(){
        $result = [];
        $db_titles = isset(json_decode($this->data_items,true)["texts"]) ? json_decode($this->data_items,true)["texts"] : [];
        $keywords = DbKeyword::all();
        foreach ($db_titles as $title) {
            $keyword = $keywords->where("data", "=", $title)->first();
            if ($keyword)
                $result[] = ["name" => $keyword->name,"data" => $title, "type" => "text"];
        }
        return $result;
    }
}
