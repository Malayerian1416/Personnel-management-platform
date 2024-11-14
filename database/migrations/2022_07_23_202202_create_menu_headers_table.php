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
        Schema::create('menu_headers', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained("users")->onDelete("cascade");
            $table->string("name");
            $table->string("short_name")->nullable();
            $table->string("slug");
            $table->string("icon")->nullable();
            $table->boolean("admin_only")->default(0);
            $table->boolean("staff_only")->default(0);
            $table->boolean("user_only")->default(0);
            $table->integer("priority")->default(0);
            $table->boolean("inactive")->default(0);
            $table->timestamps();
            $table->timestamp("deleted_at")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_headers');
    }
};
