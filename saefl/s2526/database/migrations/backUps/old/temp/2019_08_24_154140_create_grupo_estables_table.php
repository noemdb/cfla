<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGrupoEstablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupo_estables', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('Nombre');
            $table->string('code')->nullable()->comment('Código');
            $table->string('code_sm')->nullable()->comment('Abreviatura');
            $table->smallInteger('hour_t_week')->nullable()->comment('Número de horas teóricas dictadas en la semana');
            $table->smallInteger('hour_p_week')->nullable()->comment('Número de horas prácticas dictadas en la semana');
            $table->string('description')->nullable()->comment('Descripción');
            $table->string('observations')->nullable()->comment('Observaciones');
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
        Schema::dropIfExists('grupo_estables');
    }
}
