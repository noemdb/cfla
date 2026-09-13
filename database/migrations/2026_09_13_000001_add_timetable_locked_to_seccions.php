<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seccions', function (Blueprint $table): void {
            $table->boolean('timetable_locked')->default(false)->after('status_inscription_affects');
        });
    }

    public function down(): void
    {
        Schema::table('seccions', function (Blueprint $table): void {
            $table->dropColumn('timetable_locked');
        });
    }
};
