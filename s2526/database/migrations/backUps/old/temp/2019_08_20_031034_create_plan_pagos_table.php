<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanpagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planpagos', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('currency_id')->unsigned()->comment('Moneda');
            $table->string('name')->comment('Nombre del plan de pago');
            $table->string('description')->comment('Descripción del plan de pago');
            $table->string('observations')->comment('Observaciones del plan de pago');
            $table->enum('status_active',['true','false'])->default('true')->comment('Estado');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade')->onUpdate('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('planpagos');
    }
}
