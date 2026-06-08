<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditoAFavorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credito_a_favors', function (Blueprint $table) {
            $table->BigIncrements('id');
            $table->bigInteger('representant_id')->unsigned()->comment('Representante');
            $table->bigInteger('estudiant_id')->unsigned()->comment('Estudiante');
            $table->bigInteger('registro_pago_id')->nullable()->unsigned()->comment('registro pagos');
            $table->bigInteger('ingreso_id')->nullable()->unsigned()->comment('Ingreso (Transacción)');
            $table->string('credito_a_favor_ids')->nullable()->comment('Crédito a favor');
            // $table->string('number_id_pay',30)->nullable()->comment('Número de la transacción');
            // $table->integer('banco_id')->nullable()->unsigned()->comment('Banco receptor del pago');
            $table->string('credito_description')->nullable()->comment('Descripción Crédito a a favor');
            $table->string('credito_observations')->nullable()->comment('Observaciones Crédito a a favor');
            $table->float('credito_ammount',12,2)->comment('Monto del Crédito');
            // $table->string('person_bill_ci')->nullable()->comment('Cédula de la Persona a quien se le registrará el pago');
            // $table->string('person_bill_name')->nullable()->comment('Nombre de la Persona a quien se le registrará el pago');
            $table->enum('status_omitted',['true','false'])->default('true')->comment('Estado');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('representant_id')->references('id')->on('representants')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('estudiant_id')->references('id')->on('estudiants')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('registro_pago_id')->references('id')->on('registro_pagos')->onDelete('cascade')->onUpdate('cascade');
            // $table->foreign('ingreso_id')->references('id')->on('ingresos')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credito_a_favors');
    }
}
