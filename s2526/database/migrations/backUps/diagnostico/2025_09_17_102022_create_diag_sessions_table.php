<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiagSessionsTable extends Migration
{
    public function up()
    {
        Schema::create('diag_sessions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('estudiant_id')->nullable();
            $table->unsignedBigInteger('pensum_id')->nullable();
            $table->timestamp('iniciado_at')->useCurrent();
            $table->timestamp('completado_at')->nullable();
            $table->integer('progreso')->default(0);
            $table->integer('total_preguntas')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('estudiant_id')->references('id')->on('estudiants');
            $table->foreign('pensum_id')->references('id')->on('pensums');

            $table->index('estudiant_id');
            $table->index('pensum_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('diag_sessions');
    }
}
