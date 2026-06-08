<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReciboCashesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recibo_cashes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('recibo_id')->unsigned()->comment('Identificador del Recibo');
            $table->string('serial')->comment('Serial');
            $table->float('ammount',32,8)->comment('Monto del billete');
            $table->float('exchange_ammount',16,5)->nullable()->comment('Monto cambiario del efectivo');
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
        Schema::dropIfExists('recibo_cashes');
    }
}
