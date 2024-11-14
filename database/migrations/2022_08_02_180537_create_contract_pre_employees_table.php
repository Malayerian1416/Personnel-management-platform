<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_pre_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId("contract_id")->constrained("contracts")->onDelete("cascade");
            $table->foreignId("user_id")->constrained("users")->onDelete("cascade");
            $table->string("name")->nullable();
            $table->string("national_code");
            $table->string("mobile")->nullable();
            $table->string("verify")->nullable();
            $table->timestamp("verify_timestamp")->nullable();
            $table->string("tracking_code")->nullable();
            $table->boolean("registered")->default(0);
            $table->timestamp("registration_date")->nullable();
            $table->boolean("to_reload")->default(0);
            $table->boolean("reloaded")->default(0);
            $table->timestamp("reload_date")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract_pre_employees');
    }
};
