<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiagReportIndicatorResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('diag_report_indicator_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('diag_reports')->onDelete('cascade');
            $table->foreignId('pensum_id'); // Verified in Phase 2 as BigInt
            $table->foreignId('indicator_id')->constrained('diag_indicators');

            $table->string('expected_level')->nullable();
            $table->string('observed_level')->nullable();
            $table->integer('gap_value')->nullable()->default(0);
            $table->string('gap_label')->nullable(); // high, medium, low, none

            $table->text('teacher_observation')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('diag_report_indicator_results');
    }
}
