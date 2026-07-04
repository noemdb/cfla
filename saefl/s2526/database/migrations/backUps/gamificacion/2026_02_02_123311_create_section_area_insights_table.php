<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionAreaInsightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('section_area_insights', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('section_area_result_id');

            $table->enum('type', ['strength', 'weakness']);

            $table->text('description');

            $table->unsignedInteger('frequency'); // cantidad de estudiantes

            $table->timestamps();

            $table->foreign('section_area_result_id')
                ->references('id')
                ->on('section_area_results')
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
        Schema::dropIfExists('section_area_insights');
    }
}
