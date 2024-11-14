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
        Schema::table('employee_data_requests', function (Blueprint $table) {
            $table->boolean("is_loaded")->default(0);
            $table->timestamp("reload_date")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_data_requests', function (Blueprint $table) {
            $table->dropColumn("is_loaded");
            $table->dropColumn("reload_date");
        });
    }
};
