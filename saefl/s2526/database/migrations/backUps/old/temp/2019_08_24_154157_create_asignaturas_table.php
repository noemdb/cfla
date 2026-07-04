<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAsignaturasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asignaturas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pestudio_id')->unsigned()->comment('Plan Estudio');
            // $table->integer('tescala_id')->unsigned()->comment('Escala de Evaluación');
            $table->string('code')->comment('Código');
            $table->string('code_sm')->comment('Abreviatura');
            $table->string('name')->comment('Nombre');
            $table->enum('tescala',['NUMÉRICA','CUALITATIVA','NUMÉRICA Y CUALITATIVA'])->comment('Tipo de Evaluación');
            $table->integer('order')->comment('Número de orden de presentaci+n de la asignatura');
            $table->integer('hour_t_week')->nullable()->comment('Número de horas teóricas dictadas en la semana');
            $table->integer('hour_p_week')->nullable()->comment('Número de horas prácticas dictadas en la semana');
            $table->integer('unid_credit')->nullable()->comment('N+mero de unidades de cr+dito');
            $table->integer('approved_credit_unir')->nullable()->comment('Unidades de Crédito Aprobadas');
            $table->enum('enable_academic_index',['true','false'])->nullable()->comment('Tomada en cuenta para índice o promedio académico');
            $table->enum('enable_lost_regulation',['true','false'])->nullable()->comment('Sujeta a pérdida por reglamento');
            $table->enum('enable_official_doc',['true','false'])->nullable()->comment('Mostrar en documentos oficiales');
            $table->enum('enable_repairable',['true','false'])->nullable()->comment('Reparable');
            $table->enum('enable_grupo_estable',['true','false'])->nullable()->default('false')->comment('Contiene grupo estable');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('pestudio_id')->references('id')->on('pestudios')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asignaturas');
    }
}
