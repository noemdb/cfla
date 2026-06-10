<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_activity_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->restrictOnDelete();
            $table->enum('status', ['NOT_STARTED', 'IN_PROGRESS', 'COMPLETED'])->default('NOT_STARTED');
            $table->decimal('completion_pct', 5, 2)->default(0);
            $table->unsignedInteger('time_spent_secs')->default(0);
            $table->dateTime('first_access_at')->nullable();
            $table->dateTime('last_access_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['activity_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_activity_progress');
    }
};
