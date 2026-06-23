<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionDiagnosticReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('section_diagnostic_reports', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('section_id');
            $table->string('diagnostic_id'); // LAP-2026-...

            $table->unsignedInteger('students_count');

            $table->decimal('global_precision_avg', 5, 2)->nullable();

            $table->enum('status', ['draft', 'final', 'archived'])->default('draft');

            $table->string('source_prompt_version')->nullable();

            $table->timestamp('generated_at')->nullable();

            $table->timestamps();

            $table->foreign('section_id')
                ->references('id')
                ->on('seccions')
                ->onDelete('cascade');

            $table->unique(['section_id', 'diagnostic_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('section_diagnostic_reports');
    }
}
