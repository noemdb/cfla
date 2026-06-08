<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiagIndicatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('diag_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competency_id')->constrained('diag_competencies')->onDelete('cascade');
            $table->string('code')->nullable();
            $table->text('description');
            $table->string('expected_level')->nullable()->comment('Enum: Insufficient, Developing, Satisfactory, Outstanding or 1-4');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diag_indicators');
    }
}
