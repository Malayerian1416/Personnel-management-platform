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
        Schema::create('employee_financial_advantages', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained("users")->onDelete("cascade");
            $table->foreignId("employee_id")->constrained("employees")->onDelete("cascade");
            $table->unsignedBigInteger("daily_wage")->default(0);
            $table->unsignedBigInteger("prior_service")->default(0);
            $table->integer("working_days")->default(0);
            $table->integer("occupational_group")->default(0);
            $table->integer("count_of_children")->default(0);
            $table->integer("effective_year");
            $table->jsonb("advantages");
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
        Schema::dropIfExists('employee_financial_advantages');
    }
};
