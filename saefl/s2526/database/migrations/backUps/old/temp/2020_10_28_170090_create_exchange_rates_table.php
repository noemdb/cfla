<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExchangeRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->smallInteger('currency_id')->unsigned()->comment('Moneda');
            $table->smallInteger('currency_referential_id')->unsigned()->comment('Moneda Referencial');
            $table->integer('user_id')->unsigned();
            $table->date('date')->unique()->comment('Fecha de la tasa de cambio');
            $table->float('ammount',20,2)->comment('Monto de la tasa de cambio');
            $table->string('source')->comment('Fuente de Información');
            $table->string('name')->nullable()->comment('Fuente de Información');
            $table->string('observations')->nullable()->comment('Observaciones');
            $table->boolean('status_official')->default(true);
            $table->timestamps();
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('currency_referential_id')->references('id')->on('referential_currencies')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('exchange_rates');
    }
}
