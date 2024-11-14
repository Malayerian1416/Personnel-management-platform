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
        Schema::create('custom_group_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId("group_id")->constrained("custom_groups")->onDelete("cascade");
            $table->foreignId("employee_id")->constrained("employees")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom_group_employees');
    }
};
