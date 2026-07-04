<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCatchmentGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catchment_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('grado_id')->unsigned()->comment('Grado del Plan de Estudio');
            $table->string('name')->comment("Nombre");
            $table->text('description')->nullable()->comment("Descripción");
            $table->smallInteger('max')->comment("Cantidad máxima de participantes");
            $table->smallInteger('min')->comment("Cantidad mínima de participantes");
            $table->boolean('status_active')->default(true);
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
        Schema::dropIfExists('catchment_groups');
    }
}
