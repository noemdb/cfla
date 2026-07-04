<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrepagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prepagos', function (Blueprint $table) {
            $table->BigIncrements('id');
            $table->BigInteger('representant_id')->unsigned()->comment('Representante');
            $table->integer('method_pay_id')->unsigned();// Depósito, transferencia electrónica, punto de venta o crédito a favor
            $table->integer('banco_id')->unsigned()->comment('Banco receptor del pago');
            $table->string('number_i_pay',30)->comment('Número de la transacción');
            $table->date('date_transaction')->comment('Fecha de la transacción');
            $table->float('ingreso_ammount',20,2)->comment('Monto del Ingreso');
            $table->string('ingreso_observations')->nullable()->comment('Observaciones del Ingreso');
            $table->string('person_bill_ci')->nullable()->comment('Cédula de la Persona a quien se le registrará el pago');
            $table->string('person_bill_name')->nullable()->comment('Nombre de la Persona a quien se le registrará el pago');
            $table->string('comment')->nullable()->comment('Comentarios');
            $table->enum('status_approved',['true','false'])->default('false')->comment('Estado de aprobación');
            $table->enum('status_apply',['true','false'])->default('false')->comment('Estado de aprobación');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('representant_id')->references('id')->on('representants')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('method_pay_id')->references('id')->on('metodo_pagos')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('banco_id')->references('id')->on('bancos')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prepagos');
    }
}
