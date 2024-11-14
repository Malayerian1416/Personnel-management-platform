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
        Schema::table('automation', function (Blueprint $table) {
            $table->integer("current_priority");
            $table->text("message")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('automation', function (Blueprint $table) {
            $table->dropColumn("current_priority");
            $table->dropColumn("message");
        });
    }
};
