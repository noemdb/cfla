<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegistroPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registro_pagos', function (Blueprint $table) {
            $table->Bigincrements('id');
            $table->bigInteger('estudiant_id')->unsigned()->comment('Estudiante');
            $table->integer('cuentaxpagar_id')->unsigned();
            $table->integer('user_id')->unsigned();
            // $table->string('person_bill_ci')->comment('Cédula de la Persona a quien se le registrará el pago');
            // $table->string('person_bill_name')->comment('Nombre de la Persona a quien se le registrará el pago');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('estudiant_id')->references('id')->on('estudiants')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('cuentaxpagar_id')->references('id')->on('cuentaxpagars')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registro_pagos');
    }
}
