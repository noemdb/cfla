<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('timetable_calendars', 'max_subjects_per_period')) {
            Schema::table('timetable_calendars', function (Blueprint $table): void {
                $table->unsignedTinyInteger('max_subjects_per_period')
                    ->default(2)
                    ->after('period_minutes');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('timetable_calendars', 'max_subjects_per_period')) {
            Schema::table('timetable_calendars', fn (Blueprint $table) => $table->dropColumn('max_subjects_per_period'));
        }
    }
};
