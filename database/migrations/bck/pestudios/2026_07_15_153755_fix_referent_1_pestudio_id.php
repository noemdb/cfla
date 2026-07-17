<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Corrige el pestudio_id del referente 1 (DM/0044 "Reforma Curricular 31059").
     *
     * El referente apuntaba a pestudio_id=3 (plan 32011, inactivo), pero todas sus
     * competencias usan pensums del plan 31059 (pestudio_id=2, activo). Esto provocaba
     * que showReferentDetail(1) filtrara por pestudio_id=3 y no encontrara competencias.
     *
     * @see https://github.com/your-repo/path (issue #xxx)
     */
    public function up(): void
    {
        DB::table('diag_referents')
            ->where('id', 1)
            ->where('pestudio_id', 3)  // safety check: solo si aún apunta al incorrecto
            ->update(['pestudio_id' => 2]);
    }

    /**
     * Revierte el cambio por si fuera necesario.
     */
    public function down(): void
    {
        DB::table('diag_referents')
            ->where('id', 1)
            ->where('pestudio_id', 2)  // safety check: solo si tiene el valor corregido
            ->update(['pestudio_id' => 3]);
    }
};
