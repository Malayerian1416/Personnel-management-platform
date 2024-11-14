<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabourLawTariff extends Model
{
    use HasFactory;
    protected $table = "labour_law_tariff";
    protected $fillable = ["user_id","name","daily_wage","household_consumables_allowance","marital_allowance","housing_purchase_allowance","child_allowance","effective_year"];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
}
