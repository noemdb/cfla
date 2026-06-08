<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('registro_pago_combinado_id')->unsigned()->comment('Registro de Pago Combinado');
            $table->bigInteger('credito_a_favor_id')->unsigned()->comment('Credito a Favor');
            $table->integer('method_pay_id')->unsigned();
            $table->integer('banco_id')->unsigned()->comment('Banco receptor del pago');
            $table->bigInteger('representant_id')->unsigned()->comment('Representate');
            $table->string('number_i_pay',30)->unique()->comment('Número de la transacción');
            $table->date('date_transaction')->comment('Fecha de la transacción');
            $table->float('ammount',16,6)->comment('Monto de la devoución');
            $table->float('ammount_exchange',16,6)->comment('Monto de la devoución cambiaria');
            $table->string('observations')->nullable()->comment('Observaciones del devoución');
            $table->timestamps();
            $table->foreign('registro_pago_combinado_id')->references('id')->on('registro_pago_combinados')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('credito_a_favor_id')->references('id')->on('credito_a_favors')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('method_pay_id')->references('id')->on('metodo_pagos')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('banco_id')->references('id')->on('bancos')->onDelete('cascade')->onUpdate('cascade'); 
            $table->foreign('representant_id')->references('id')->on('representants')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('refunds');
    }
}
