<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetSms extends Model
{
    protected $table = "password_resets_sms";
    protected $fillable = ["mobile","token","created_at"];

}
