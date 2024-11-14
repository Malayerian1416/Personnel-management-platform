<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReloadEmployeeData extends Model
{
    use HasFactory;
    protected $table = "reload_employees_data";
    protected $fillable = ["reloadable_id","reloadable_type","national_code","doc_titles","db_titles","is_loaded"];
    protected $appends = ["docs","databases"];

    public function reloadable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }
    public function GetDocsAttribute(){
        $result = [];
        $docs = json_decode($this->doc_titles) ?: [] ;
        $keywords = DbKeyword::all();
        foreach ($docs as $doc) {
            $keyword = $keywords->where("data", "=", $doc)->first();
            if ($keyword)
                $result[] = ["name" => $keyword->name, "data" => $doc, "type" => "file"];
        }
        return $result;
    }
    public function GetDatabasesAttribute(){
        $result = [];
        $db_titles = json_decode($this->db_titles) ?: [];
        $keywords = DbKeyword::all();
        foreach ($db_titles as $title) {
            $keyword = $keywords->where("data", "=", $title)->first();
            if ($keyword)
                $result[] = ["name" => $keyword->name, "data" => $title, "type" => "text"];
        }
        return $result;
    }

}
