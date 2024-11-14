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
        Schema::create('labour_law_tariff', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained("users")->onDelete("cascade");
            $table->string("name");
            $table->unsignedBigInteger("daily_wage");
            $table->unsignedBigInteger("household_consumables_allowance");
            $table->unsignedBigInteger("housing_purchase_allowance");
            $table->unsignedBigInteger("child_allowance");
            $table->integer("effective_year");
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
        Schema::dropIfExists('labour_law_tariff');
    }
};
