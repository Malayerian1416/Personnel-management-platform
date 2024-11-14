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
        Schema::create('reload_employees_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("reloadable_id");
            $table->string("reloadable_type");
            $table->string("national_code");
            $table->json("doc_titles")->nullable();
            $table->json("db_titles")->nullable();
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
        Schema::dropIfExists('reload_employees_data');
    }
};
