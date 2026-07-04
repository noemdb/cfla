<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanBeneficosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_beneficos', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('estudiant_id')->unsigned()->comment('Estudiante');
            $table->integer('descuento_id')->unsigned()->comment('Descuentos');
            // $table->integer('plan_pago_id')->unsigned();
            $table->string('name')->nullable()->comment('Nombre del Plan Benéfico');
            $table->string('observations')->nullable()->comment('Observaciones');
            $table->enum('status_active',['true','false'])->default('true')->comment('Estado');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('estudiant_id')->references('id')->on('estudiants')->onDelete('cascade')->onUpdate('cascade');
            // $table->foreign('plan_pago_id')->references('id')->on('planpagos')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('descuento_id')->references('id')->on('descuentos')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan_beneficos');
    }
}
