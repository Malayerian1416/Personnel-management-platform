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
        Schema::table('unregistered_employees', function (Blueprint $table) {
            $table->dropColumn("organization");
            $table->foreignId("organization_id")->constrained("organizations")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unregistered_employees', function (Blueprint $table) {
            $table->string("organization");
            $table->dropForeign("organizations_organization_id_foreign");
            $table->dropColumn("organization_id");
        });
    }
};
