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
            $table->foreignId("current_role_id")->nullable()->change();
            $table->integer("current_priority")->nullable()->change();
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
            $table->foreignId("current_role_id")->constrained("roles")->onDelete("cascade");
            $table->integer("current_priority")->change();
        });
    }
};
