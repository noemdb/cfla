<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusInscriptionAffectsToSeccions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('seccions', function (Blueprint $table) {
            $table->enum('status_inscription_affects',['true','false'])->after('status_active')->default('true')->comment('Contabiliza Inscripción');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seccions', function (Blueprint $table) {
            $table->dropColumn('status_inscription_affects');
        });
    }
}
