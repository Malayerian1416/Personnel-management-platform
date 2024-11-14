<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomesticNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domestic_news', function (Blueprint $table) {
            $table->id();
            $table->text("title")->nullable();
            $table->text("short_desc")->nullable();
            $table->longText("description")->nullable();
            $table->string("release_date",255)->nullable();
            $table->unsignedBigInteger("view")->nullable();
            $table->boolean("publish")->default(1);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
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
        Schema::dropIfExists('domestic_news');
    }
}
