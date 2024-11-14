<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsDelivery extends Model
{
    use HasFactory;
    protected $table = "sms_delivery";
    protected $fillable = ["sent_id","delivery_result"];
}
