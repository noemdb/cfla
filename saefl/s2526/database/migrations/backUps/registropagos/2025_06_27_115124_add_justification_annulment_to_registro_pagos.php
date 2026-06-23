<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJustificationAnnulmentToRegistroPagos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registro_pagos', function (Blueprint $table) {
            $table->text('justification_annulment')->nullable()->after('cancellable')->comment('Justificacion de la anulacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registro_pagos', function (Blueprint $table) {
            $table->dropColumn('justification_annulment');
        });
    }
}
