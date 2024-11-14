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
        Schema::table('settlement_form_applications', function (Blueprint $table) {
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
        Schema::table('settlement_form_applications', function (Blueprint $table) {
            $table->dropColumn("i_number");
            $table->dropColumn("data");
        });
    }
};
