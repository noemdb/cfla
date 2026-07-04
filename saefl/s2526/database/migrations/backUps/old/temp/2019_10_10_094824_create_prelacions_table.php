<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrelacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prelacions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('asignatura_id')->unsigned()->comment('Asignatura');
            $table->integer('asignatura_p_id')->unsigned()->comment('Asignatura con prelación');
            $table->timestamps();
            $table->foreign('asignatura_id')->references('id')->on('asignaturas')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prelacions');
    }
}
