<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegistroPagoCombinadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registro_pago_combinados', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('representant_id')->unsigned()->default('1111111111')->comment('Cédula de identidad, Id temporal o pasaporte Representate');
            $table->string('description')->comment('Descripción del registro de pago combinado');

            $table->softDeletes();
            $table->timestamps();
            $table->foreign('representant_id')->references('id')->on('representants')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registro_pago_combinados');
    }
}
