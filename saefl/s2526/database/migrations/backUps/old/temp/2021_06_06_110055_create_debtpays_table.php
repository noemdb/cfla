<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDebtpaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('debtpays', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('estudiant_id')->unsigned()->comment('Estudiante');
            $table->integer('cuentaxpagar_id')->unsigned();
            $table->float('ammount',16,2)->comment('Monto');
            $table->float('exchange_ammount',10,6)->comment('Monto Cambiario');

            $table->timestamps();

            $table->foreign('estudiant_id')->references('id')->on('estudiants')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('cuentaxpagar_id')->references('id')->on('cuentaxpagars')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('debtpays');
    }
}
