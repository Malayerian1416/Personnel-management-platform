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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId("employee_id")->constrained("employees")->onDelete("cascade");
            $table->foreignId("expert_id")->constrained("users")->onDelete("cascade");
            $table->foreignId("room_id")->constrained("ticket_rooms")->onDelete("cascade");
            $table->string("sender");
            $table->text("message");
            $table->string("attachment")->nullable();
            $table->boolean("is_read")->default(0);
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
        Schema::dropIfExists('tickets');
    }
};
