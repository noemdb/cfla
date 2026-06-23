<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIngresoFraccionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingreso_fraccions', function (Blueprint $table) {
            $table->BigIncrements('id');
            $table->bigInteger('registro_pago_id')->unsigned()->comment('registro pagos');
            $table->bigInteger('ingreso_id')->unsigned()->comment('Ingreso');
            $table->float('fraccion_ammount',16,2)->comment('Monto de la fracción');
            $table->float('fraccion_exchange',12,8)->comment('Monto de la fracción exchange');
            $table->timestamps();
            $table->foreign('registro_pago_id')->references('id')->on('registro_pagos')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('ingreso_id')->references('id')->on('ingresos')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ingreso_fraccions');
    }
}
