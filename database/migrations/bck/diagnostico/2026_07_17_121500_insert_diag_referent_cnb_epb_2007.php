<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

return new class extends Migration
{
    /**
     * Inserta el referente curricular "Currículo del Subsistema de Educación
     * Primaria Bolivariana" (CNB-EPB-2007) en la tabla diag_referents.
     *
     * Este es el Diseño Curricular oficial emitido por el MPPE en Venezuela
     * (septiembre 2007), asociado al plan de estudio con pestudio_id=1.
     */
    public function up(): void
    {
        $now = Carbon::create(2026, 7, 18, 0, 0, 0);

        DB::table('diag_referents')->updateOrInsert(
            ['code' => 'CNB-EPB-2007'],
            [
                'pestudio_id' => 1,
                'name'        => 'Currículo del Subsistema de Educación Primaria Bolivariana',
                'version'     => '2007.1',
                'description' => 'Diseño Curricular oficial del Subsistema de Educación Primaria Bolivariana en Venezuela, emitido por el Ministerio del Poder Popular para la Educación (MPPE) en septiembre de 2007.',
                'active'      => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]
        );
    }

    /**
     * Revierte la inserción eliminando el registro.
     */
    public function down(): void
    {
        DB::table('diag_referents')
            ->where('code', 'CNB-EPB-2007')
            ->delete();
    }
};
