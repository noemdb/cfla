<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditoAplicadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credito_aplicados', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('registro_pago_id')->unsigned();
            $table->bigInteger('credito_a_favor_id')->unique()->unsigned();
            $table->string('credito_aplicado_observations')->nullable()->comment('Observaciones Creditos a favor Aplicados');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('registro_pago_id')->references('id')->on('registro_pagos')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('credito_a_favor_id')->references('id')->on('credito_a_favors')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credito_aplicados');
    }
}
