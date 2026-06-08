<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMbancariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mbancarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('banco_id')->unsigned()->comment('Banco receptor del pago');
            $table->string('number_i_pay',30)->comment('Número de la transacción');
            $table->date('date_transaction')->comment('Fecha de la transacción');
            $table->float('ingreso_ammount',20,2)->comment('Monto del Ingreso');
            $table->timestamps();
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
        Schema::dropIfExists('mbancarios');
    }
}
