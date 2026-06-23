<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lms_activity_resources', function (Blueprint $table) {
            $table->foreignId('section_id')
                ->nullable()
                ->after('activity_id')
                ->constrained('lms_activity_sections')
                ->cascadeOnDelete();
            $table->index('section_id');
        });

        Schema::table('lms_activity_links', function (Blueprint $table) {
            $table->foreignId('section_id')
                ->nullable()
                ->after('activity_id')
                ->constrained('lms_activity_sections')
                ->cascadeOnDelete();
            $table->index('section_id');
        });
    }

    public function down(): void
    {
        Schema::table('lms_activity_links', function (Blueprint $table) {
            $table->dropIndex(['section_id']);
            $table->dropForeign(['section_id']);
            $table->dropColumn('section_id');
        });

        Schema::table('lms_activity_resources', function (Blueprint $table) {
            $table->dropIndex(['section_id']);
            $table->dropForeign(['section_id']);
            $table->dropColumn('section_id');
        });
    }
};
