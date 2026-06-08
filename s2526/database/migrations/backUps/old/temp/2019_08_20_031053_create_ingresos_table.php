<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIngresosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingresos', function (Blueprint $table) {
            $table->BigIncrements('id');
            $table->BigInteger('registro_pago_id')->nullable()->unsigned()->comment('Estudiante');
            $table->BigInteger('estudiant_id')->unsigned()->comment('Estudiante');
            $table->integer('method_pay_id')->unsigned();// Depósito, transferencia electrónica, punto de venta o crédito a favor
            $table->integer('banco_id')->unsigned()->comment('Banco receptor del pago');
            $table->string('number_i_pay',30)->comment('Número de la transacción');
            // $table->string('number_i_pay',30)->unique()->comment('Número de la transacción');
            $table->date('date_transaction')->comment('Fecha de la transacción');
            $table->float('ingreso_ammount',8,2)->comment('Monto del Ingreso');
            $table->string('ingreso_observations')->nullable()->comment('Observaciones del Ingreso');
            $table->string('person_bill_ci')->nullable()->comment('Cédula de la Persona a quien se le registrará el pago');
            $table->string('person_bill_name')->nullable()->comment('Nombre de la Persona a quien se le registrará el pago');
            $table->softDeletes();
            $table->timestamps();
            // $table->foreign('registro_pago_id')->references('id')->on('registro_pagos')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('estudiant_id')->references('id')->on('estudiants')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('ingresos');
    }
}
