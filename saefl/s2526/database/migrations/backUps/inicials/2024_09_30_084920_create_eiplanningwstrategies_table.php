<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEiplanningwstrategiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eiplanningwstrategies', function (Blueprint $table) {
            $table->id(); // ID único para la estrategia
            $table->unsignedBigInteger('eiplanningwk_id'); // Relación con la planificación semanal
            $table->string('momento_rutina_diaria')->nullable(); // Momento de la Rutina Diaria
            $table->text('lunes')->nullable(); // Estrategia del lunes
            $table->text('martes')->nullable(); // Estrategia del martes
            $table->text('miercoles')->nullable(); // Estrategia del miércoles
            $table->text('jueves')->nullable(); // Estrategia del jueves
            $table->text('viernes')->nullable(); // Estrategia del viernes
            $table->timestamps(); // created_at y updated_at

            // Llave foránea para la relación con la tabla eiplanningwks
            // $table->foreign('eiplanningwk_id')->references('id')->on('eiplanningwks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eiplanningwstrategies');
    }
}
