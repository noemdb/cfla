<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('timetable_calendar_versions')) {
            Schema::create('timetable_calendar_versions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('calendar_id')->constrained('timetable_calendars')->cascadeOnDelete();
                $table->unsignedInteger('version');
                $table->string('status', 20);
                $table->unsignedBigInteger('published_by')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->decimal('quality_score', 8, 2)->nullable();
                $table->json('summary_json')->nullable();
                $table->timestamps();

                $table->unique(['calendar_id', 'version'], 'uq_timetable_calendar_version');
                $table->index(['calendar_id', 'created_at'], 'idx_timetable_versions_calendar_created');
            });
        }

        if (! Schema::hasTable('timetable_change_logs')) {
            Schema::create('timetable_change_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('calendar_id')->constrained('timetable_calendars')->cascadeOnDelete();
                $table->foreignId('version_id')->nullable()->constrained('timetable_calendar_versions')->nullOnDelete();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('action', 60);
                $table->unsignedBigInteger('lesson_id')->nullable();
                $table->json('before_json')->nullable();
                $table->json('after_json')->nullable();
                $table->json('metadata_json')->nullable();
                $table->timestamps();

                $table->index(['calendar_id', 'created_at'], 'idx_timetable_changes_calendar_created');
                $table->index(['calendar_id', 'lesson_id'], 'idx_timetable_changes_calendar_lesson');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_change_logs');
        Schema::dropIfExists('timetable_calendar_versions');
    }
};
