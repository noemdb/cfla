<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedagogicalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedagogicals', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Identificador único de la aplicación del instrumento (clave primaria)');
            $table->unsignedBigInteger('lesson_id')->comment('Clave foránea que referencia el campo `id` de la tabla `lessons`');
            $table->text('instructions')->nullable()->comment('Instrucciones para la aplicación del instrumento pedagógico');
            $table->text('observations')->nullable()->comment('Observaciones sobre la aplicación del instrumento pedagógico');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedagogicals');
    }
}
