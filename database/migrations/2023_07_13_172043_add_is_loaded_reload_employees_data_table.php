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
        Schema::table('reload_employees_data', function (Blueprint $table) {
            $table->boolean("is_loaded")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reload_employees_data', function (Blueprint $table) {
            $table->dropColumn("is_loaded");
        });
    }
};
