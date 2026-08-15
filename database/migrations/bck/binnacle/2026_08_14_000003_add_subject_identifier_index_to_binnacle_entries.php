<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // El filtro "usuario" del panel busca por subject_identifier (varchar 100):
        // el índice permite igualdad y LIKE con prefijo sin full scan.
        DB::statement('ALTER TABLE binnacle_entries ADD INDEX idx_subject_identifier (subject_identifier(20))');
        DB::statement('ALTER TABLE binnacle_entries_archive ADD INDEX idx_subject_identifier (subject_identifier(20))');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE binnacle_entries DROP INDEX idx_subject_identifier');
        DB::statement('ALTER TABLE binnacle_entries_archive DROP INDEX idx_subject_identifier');
    }
};
