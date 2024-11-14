<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanPaymentConfirmationApplication extends Model
{
    use softDeletes;
    protected $table = "loan_payment_confirmation_applications";
    protected $fillable = ["user_id","employee_id","recipient","borrower","loan_amount","is_accepted","is_refused","inactive","i_number","data"];
    protected $appends = ["data_array"];

    public function form(): MorphOne
    {
        return $this->morphOne(FormTemplate::class, 'formable');
    }
    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class,"employee_id");
    }
    public function automation(): MorphOne
    {
        return $this->morphOne(Automation::class, 'automationable');
    }
    public function GetDataArrayAttribute(){
        return json_decode($this->data,true);
    }
}
