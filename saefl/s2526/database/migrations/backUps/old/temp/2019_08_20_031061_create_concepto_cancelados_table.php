<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConceptoCanceladosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concepto_cancelados', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('registro_pago_id')->unsigned();
            $table->integer('concepto_pago_id')->unsigned();
            $table->string('concepto_pago_observations')->nullable()->comment('Observaciones Conceptos Cancelados');            
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('registro_pago_id')->references('id')->on('registro_pagos')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('concepto_pago_id')->references('id')->on('concepto_pagos')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('concepto_cancelados');
    }
}
