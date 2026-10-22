<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Visibilidad de los nombres de docentes en los reportes imprimibles del
 * calendario. Por defecto se conserva el comportamiento histórico: mostrar
 * los nombres.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('timetable_calendars', 'status_profesor_show')) {
            Schema::table('timetable_calendars', function (Blueprint $table): void {
                $table->boolean('status_profesor_show')
                    ->default(true)
                    ->after('max_subjects_per_period');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('timetable_calendars', 'status_profesor_show')) {
            Schema::table('timetable_calendars', fn (Blueprint $table) => $table->dropColumn('status_profesor_show'));
        }
    }
};
