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
        Schema::table('occupational_medicine_applications', function (Blueprint $table) {
            $table->string("i_number");
            $table->jsonb("data");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('occupational_medicine_applications', function (Blueprint $table) {
            $table->dropColumn("i_number");
            $table->dropColumn("data");
        });
    }
};
