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
            $table->foreignId("employee_id")->constrained("employees")->onDelete("cascade");
            $table->foreignId("contract_id")->constrained("contracts")->onDelete("cascade");
            $table->boolean("editable")->default(1);
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
            $table->dropForeign("automation_employee_id_foreign");
            $table->dropForeign("automation_contract_id_foreign");
            $table->dropColumn("employee_id");
            $table->dropColumn("contract_id");
            $table->dropColumn("editable");
        });
    }
};
