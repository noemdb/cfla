<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('timetable_calendars', 'strategy')) {
            Schema::table('timetable_calendars', function (Blueprint $table): void {
                $table->string('strategy', 32)
                    ->default('optimized')
                    ->after('max_subjects_per_period');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('timetable_calendars', 'strategy')) {
            Schema::table('timetable_calendars', fn (Blueprint $table) => $table->dropColumn('strategy'));
        }
    }
};
