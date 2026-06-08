<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReciboPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recibo_pagos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('recibo_id')->unsigned()->comment('Identificador del Recibo');
            $table->string('quota')->comment('Cuota');
            $table->float('ammount',32,8)->nullable()->comment('Monto del Pago');
            $table->float('exchange_ammount',16,5)->nullable()->comment('Monto cambiario del pago');
            $table->timestamps();
            $table->foreign('recibo_id')->references('id')->on('recibos')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recibo_pagos');
    }
}
