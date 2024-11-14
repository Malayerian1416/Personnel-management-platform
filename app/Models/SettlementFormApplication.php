<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class SettlementFormApplication extends Model
{
    use softDeletes;
    protected $table = "settlement_form_applications";
    protected $fillable = ["user_id","employee_id","is_accepted","is_refused","inactive","i_number","data"];
    protected $appends = ["data_array"];

    public function automation(): MorphOne
    {
        return $this->morphOne(Automation::class, 'automationable');
    }

    public function GetDataArrayAttribute(){
        return json_decode($this->data,true);
    }
}
