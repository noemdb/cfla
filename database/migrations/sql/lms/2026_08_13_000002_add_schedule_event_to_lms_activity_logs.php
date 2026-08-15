<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE lms_activity_logs MODIFY COLUMN `event` ENUM(
            'VIEW',
            'CONTENT_VIEW',
            'RESOURCE_DOWNLOAD',
            'PUBLISH',
            'UNPUBLISH',
            'EDIT',
            'SECTION_ADD',
            'RESOURCE_ADD',
            'RESOURCE_DELETE',
            'COMPLETE',
            'SCHEDULE'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE lms_activity_logs MODIFY COLUMN `event` ENUM(
            'VIEW',
            'CONTENT_VIEW',
            'RESOURCE_DOWNLOAD',
            'PUBLISH',
            'UNPUBLISH',
            'EDIT',
            'SECTION_ADD',
            'RESOURCE_ADD',
            'RESOURCE_DELETE',
            'COMPLETE'
        ) NOT NULL");
    }
};
