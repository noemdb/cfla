<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Amplía `activities.teaching` de TEXT (65.535 bytes) a MEDIUMTEXT
     * (16.777.215 bytes) para soportar enseñanzas de hasta 30.000 caracteres
     * sin riesgo de truncamiento con utf8mb4 (30.000 × 4 = 120.000 bytes).
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE `activities` MODIFY `teaching` MEDIUMTEXT NULL COMMENT \'Enseñanza\'');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `activities` MODIFY `teaching` TEXT NULL COMMENT \'Enseñanza\'');
    }
};
