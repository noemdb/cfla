<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Índices de solo-lectura para acelerar el scope de liderazgo.
     * No tocan datos existentes ni cambian tipos de columna: son
     * seguros de aplicar en caliente sobre una BD en producción.
     */
    public function up(): void
    {
        $this->addIndexIfTableExists('area_conocimientos', 'leader_id', 'area_conocimientos_leader_id_index');
        $this->addIndexIfTableExists('campo_conocimientos', 'area_conocimiento_id', 'campo_conocimientos_area_conocimiento_id_index');
        $this->addIndexIfTableExists('campo_conocimientos', 'asignatura_id', 'campo_conocimientos_asignatura_id_index');
        $this->addIndexIfTableExists('pensums', 'asignatura_id', 'pensums_asignatura_id_index');
        $this->addIndexIfTableExists('pevaluacions', 'pensum_id', 'pevaluacions_pensum_id_index');
    }

    public function down(): void
    {
        $this->dropIndexIfExists('area_conocimientos', 'leader_id');
        $this->dropIndexIfExists('campo_conocimientos', 'area_conocimiento_id');
        $this->dropIndexIfExists('campo_conocimientos', 'asignatura_id');
        $this->dropIndexIfExists('pensums', 'asignatura_id');
        $this->dropIndexIfExists('pevaluacions', 'pensum_id');
    }

    private function addIndexIfTableExists(string $table, string $column, string $indexName): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        if (!$this->hasIndex($table, $indexName)) {
            Schema::table($table, function (Blueprint $t) use ($column) {
                $t->index($column);
            });
        }
    }

    private function dropIndexIfExists(string $table, string $column): void
    {
        if (Schema::hasTable($table)) {
            Schema::table($table, function (Blueprint $t) use ($column) {
                $t->dropIndex([$column]);
            });
        }
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        $indexes = Schema::getIndexes($table);
        foreach ($indexes as $index) {
            if ($index['name'] === $indexName) {
                return true;
            }
        }
        return false;
    }
};
