<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->BigIncrements('id');
            $table->bigInteger('registro_pago_id')->unsigned()->comment('registro pagos');
            $table->bigInteger('ingreso_id')->unsigned()->comment('Ingreso');
            $table->float('pagos_ammount',8,2)->comment('Monto del Pago');
            $table->softDeletes();
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
        Schema::dropIfExists('pagos');
    }
}
