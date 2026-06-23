<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConceptoPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concepto_pagos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cuentaxpagar_id')->unsigned();
            $table->integer('nom_concepto_pago_id')->unsigned()->comment('Nombre del concepto');
            $table->string('concepto_description')->nullable()->comment('Descripción del concepto');
            $table->string('concepto_observations')->nullable()->comment('Observaciones de la Cuenta por Pagar');
            $table->float('concepto_ammount',8,2)->comment('Monto');
            $table->enum('status_modifiable',['true','false'])->nullable()->default('true')->comment('Permite manipular el costo al facturar');
            $table->enum('status_discount',['true','false'])->nullable()->default('true')->comment('Permite aplicación de descuentos - planes benéficos');
            $table->enum('status_active',['true','false'])->nullable()->default('true')->comment('Permite manipular el costo al facturar');
            $table->enum('status_annuity',['true','false'])->nullable()->default('true')->comment('Anualidad');
            // $table->enum('status_finance',['true','false'])->default('true')->comment('Permite financiamiento');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('cuentaxpagar_id')->references('id')->on('cuentaxpagars')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('nom_concepto_pago_id')->references('id')->on('nom_concepto_pagos')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('concepto_pagos');
    }
}
