<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusAdvidersToRepresentantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('representants', function (Blueprint $table) {
            $table->enum('status_adviders',['true','false'])->after('status_active')->default('false')->comment('Delegado');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('representants', function (Blueprint $table) {
            $table->dropColumn('status_adviders');
        });
    }
}
