<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiagRecommendationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diag_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('diag_reports')->onDelete('cascade');
            $table->foreignId('pensum_id')->nullable();

            $table->string('type')->comment('area, transversal, followup');
            $table->text('recommendation');
            $table->string('priority')->comment('low, medium, high');
            $table->string('suggested_frequency')->nullable()->comment('daily, weekly, etc.');

            $table->boolean('active')->default(true);

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
        Schema::dropIfExists('diag_recommendations');
    }
}
