<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiagCompetenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('diag_competencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referent_id')->constrained('diag_referents')->onDelete('cascade');
            $table->unsignedBigInteger('pensum_id')->nullable()->comment('Relacion con area/asignatura');
            // $table->foreign('pensum_id')->references('id')->on('pensums'); // Optional: Add strict FK if desired
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diag_competencies');
    }
}
