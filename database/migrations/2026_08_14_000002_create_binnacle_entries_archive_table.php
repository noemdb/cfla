<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Réplica exacta de binnacle_entries (columnas, índices y únicos) sin
        // los triggers de inmutabilidad. El comando binnacle:archive mueve aquí
        // las filas que superan la retención por categoría (Spec §12).
        DB::statement('CREATE TABLE binnacle_entries_archive LIKE binnacle_entries');

        Schema::table('binnacle_entries_archive', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->after('created_by');
            $table->index('archived_at', 'idx_archived_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('binnacle_entries_archive');
    }
};
