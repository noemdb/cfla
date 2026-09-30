<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix huérfanas: DiagQuestion con diag_main_id NULL/0/''
     * en el ámbito is_leadership mostraban Total 80 vs 71 al filtrar.
     * Se asignan a diag_main_id = 1 (Diagnóstico Educativo. Aproximación...).
     */
    public function up(): void
    {
        // Solo huérfanas con pensum válido y diag_main_id nulo/vacío/0
        DB::table('diag_questions')
            ->where(function ($q) {
                $q->whereNull('diag_main_id')
                  ->orWhere('diag_main_id', 0)
                  ->orWhere('diag_main_id', '');
            })
            ->update(['diag_main_id' => 1]);

        // Log para auditoría (opcional)
        $count = DB::table('diag_questions')->where('diag_main_id', 1)->count();
        // No se lanza excepción si ya estaban asignadas
    }

    public function down(): void
    {
        // No se revierte automáticamente: las huérfanas quedarían sin diagnóstico.
        // Si se requiere rollback manual, usar backup previo.
    }
};
