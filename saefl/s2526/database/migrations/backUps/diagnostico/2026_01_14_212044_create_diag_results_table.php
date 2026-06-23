<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiagResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('diag_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('diag_reports')->onDelete('cascade');

            $table->integer('total_answered_questions')->default(0);
            $table->decimal('precision', 5, 2)->nullable();
            $table->string('open_ended_response_level')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diag_results');
    }
}
