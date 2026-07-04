<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecursosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recursos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->BigInteger('registro_pago_combinado_id')->unsigned()->comment('ingreso');
            $table->BigInteger('ingreso_id')->unique()->nullable()->unsigned()->comment('ingreso');
            $table->BigInteger('credito_a_favor_id')->unique()->nullable()->unsigned()->comment('CAF');

            $table->enum('status_ingreso',['true','false'])->nullable()->comment('Ingresos - Transferencias');
            $table->enum('status_abono',['true','false'])->nullable()->comment('Abonos');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('registro_pago_combinado_id')->references('id')->on('registro_pago_combinados')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('ingreso_id')->references('id')->on('ingresos')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('credito_a_favor_id')->references('id')->on('credito_a_favors')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recursos');
    }
}
