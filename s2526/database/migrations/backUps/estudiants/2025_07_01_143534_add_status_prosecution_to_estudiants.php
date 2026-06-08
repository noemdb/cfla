<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusProsecutionToEstudiants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estudiants', function (Blueprint $table) {
            $table->text('status_prosecution')->nullable()->default(false)->after('token')->comment('Proseguir/continuar al siguiente periodo escolar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('estudiants', function (Blueprint $table) {
            $table->dropColumn('status_prosecution');
        });
    }
}
