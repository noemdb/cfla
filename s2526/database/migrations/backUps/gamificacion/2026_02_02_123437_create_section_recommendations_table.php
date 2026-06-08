<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionRecommendationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('section_recommendations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('section_diagnostic_report_id');

            $table->enum('type', ['FAMILIAR', 'DOCENTE', 'ESTUDIANTE']);
            $table->enum('priority', ['HIGH', 'MEDIUM', 'LOW']);

            $table->text('recommendation');

            $table->timestamps();

            $table->foreign('section_diagnostic_report_id')
                ->references('id')
                ->on('section_diagnostic_reports')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('section_recommendations');
    }
}
