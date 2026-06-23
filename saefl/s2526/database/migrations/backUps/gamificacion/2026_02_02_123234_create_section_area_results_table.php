<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionAreaResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('section_area_results', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('section_diagnostic_report_id');

            $table->string('subject_id'); // SUBJ-xxx
            $table->string('area_name');

            $table->json('level_distribution');
            $table->decimal('precision_avg', 5, 2)->nullable();

            $table->json('dominant_errors')->nullable();

            $table->text('observation');

            $table->timestamps();

            $table->foreign('section_diagnostic_report_id')
                ->references('id')
                ->on('section_diagnostic_reports')
                ->onDelete('cascade');

            $table->index(['subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('section_area_results');
    }
}
