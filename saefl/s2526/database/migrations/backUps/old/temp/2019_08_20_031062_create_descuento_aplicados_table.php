<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDescuentoAplicadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('descuento_aplicados', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('registro_pago_id')->unsigned();
            $table->integer('descuento_id')->unsigned();
            $table->string('descuento_aplicado_observations')->nullable()->comment('Observaciones Descuentos Aplicados');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('registro_pago_id')->references('id')->on('registro_pagos')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('descuento_id')->references('id')->on('descuentos')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('descuento_aplicados');
    }
}
