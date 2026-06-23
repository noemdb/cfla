<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBaremosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('baremos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pestudio_id')->unsigned()->comment('Plan Estudio');
            $table->bigInteger('pensum_id')->unsigned()->nullable()->comment('Asignatura');
            $table->float('minimo',5,2)->comment('Calificación mínima');
            $table->float('maxima',5,2)->comment('Calificación máxima');
            $table->string('valoracion')->comment('Valoración');
            $table->string('description')->nullable()->comment('Descripción');
            $table->enum('literal',['A','B','C','D','E','F','G','H','I'])->comment('Literal');

            $table->foreign('pestudio_id')->references('id')->on('pestudios')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('pensum_id')->references('id')->on('pensums')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('baremos');
    }
}
