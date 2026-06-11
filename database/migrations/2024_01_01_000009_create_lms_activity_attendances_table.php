<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_activity_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->restrictOnDelete();
            $table->unsignedInteger('student_id');
            $table->foreign('student_id')->references('id')->on('users')->restrictOnDelete();
            $table->unsignedInteger('recorded_by');
            $table->foreign('recorded_by')->references('id')->on('users')->restrictOnDelete();
            $table->enum('status', ['PRESENT', 'LATE', 'ABSENT', 'EXCUSED', 'REMOTE'])->default('ABSENT');
            $table->text('observation')->nullable();
            $table->dateTime('checked_in_at')->nullable();
            $table->timestamps();
            $table->unique(['activity_id', 'student_id']);
            $table->index(['activity_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_activity_attendances');
    }
};
