<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCuentaXPagarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuentaxpagars', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('planpago_id')->unsigned();
            $table->string('name')->comment('Nombre de la Cuenta por Pagar');
            $table->enum('type',['GENERAL','INDIVIDUAL'])->default('GENERAL')->comment('Estado');
            $table->bigInteger('estudiant_id')->unsigned()->nullable()->comment('Estudiante');
            $table->date('date_expiration')->comment('Fecha de vencimiento');
            $table->string('description')->nullable()->comment('Descripción de la Cuenta por Pagar');
            $table->string('observations')->nullable()->comment('Observaciones de la Cuenta por Pagar');
            $table->enum('status_active',['true','false'])->default('true')->comment('Estado');            
            $table->enum('status_inscription',['true','false'])->default('false')->comment('La cancelación del cuenta por pagar estable la inscripción administrativa');            
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('planpago_id')->references('id')->on('planpagos')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cuentaxpagars');
    }
}
