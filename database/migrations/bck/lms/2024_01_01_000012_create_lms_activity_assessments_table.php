<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_activity_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->unsignedInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('assessment_type', ['QUIZ', 'EXAM', 'PRACTICE', 'SURVEY'])->default('QUIZ');
            $table->decimal('max_score', 8, 2)->default(100);
            $table->decimal('passing_score', 8, 2)->nullable();
            $table->unsignedSmallInteger('time_limit_min')->nullable();
            $table->unsignedTinyInteger('attempts_max')->default(1);
            $table->boolean('randomize')->default(false);
            $table->boolean('show_results')->default(true);
            $table->dateTime('available_from')->nullable();
            $table->dateTime('available_until')->nullable();
            $table->enum('status', ['DRAFT', 'PUBLISHED', 'CLOSED'])->default('DRAFT');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['activity_id', 'status', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_activity_assessments');
    }
};
