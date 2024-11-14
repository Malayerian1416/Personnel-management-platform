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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->nullable()->constrained("users");
            $table->string('name');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('email')->unique();
            $table->string('mobile')->unique();
            $table->string("avatar")->nullable();
            $table->enum("gender",['m','f'])->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('mobile_verified_at')->nullable();
            $table->boolean("is_super_user")->default(0);
            $table->boolean("is_admin")->default(0);
            $table->boolean("is_staff")->default(0);
            $table->boolean("is_user")->default(0);
            $table->rememberToken();
            $table->string("sign")->nullable();
            $table->string("sign_hash")->nullable();
            $table->boolean("inactive")->default(0);
            $table->timestamp("last_activity")->nullable();
            $table->string("last_ip_address")->nullable();
            $table->timestamp("deleted_at")->nullable();
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
        Schema::dropIfExists('users');
    }
};
