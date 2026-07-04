<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionGlobalResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('section_global_results', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('section_diagnostic_report_id');

            $table->text('global_summary');

            $table->json('open_ended_response_level_distribution')->nullable();
            $table->json('precision_distribution')->nullable();

            $table->decimal('total_questions_avg', 6, 2)->nullable();

            $table->enum('confidence_level', ['LOW', 'MEDIUM', 'HIGH'])->nullable();

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
        Schema::dropIfExists('section_global_results');
    }
}
