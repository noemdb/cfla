<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstrumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instruments', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Identificador único de la vinculación (clave primaria)');
            $table->unsignedBigInteger('pedagogical_id')->comment('Clave foránea que referencia el campo `id` de la tabla `pedagogicals`');
            $table->unsignedBigInteger('teaching_id')->comment('Clave foránea que referencia el campo `id` de la tabla `teachings`');
            $table->unsignedInteger('order')->comment('Orden de aparición del instrumento pedagógico en la lección');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('teaching_id')->references('id')->on('teachings')->onDelete('cascade');
            $table->foreign('pedagogical_id')->references('id')->on('pedagogicals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('instruments');
    }
}
