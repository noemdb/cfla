<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PopulateDefaultDiagMains extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Generate Single Global DiagMain
        $diagMainId = DB::table('diag_mains')->insertGetId([
            'name' => 'Diagnóstico Educativo. Aproximación pedagogica y exploración de Competencias',
            'description' => 'Este Diagnóstico Educativo constituye una aproximación pedagógica integral y una exploración de competencias clave. Mediante un sistema automatizado, se identifican fortalezas y áreas de oportunidad de manera ágil y precisa. La información recolectada será analizada rigurosamente e incorporada al historial del estudiante, permitiendo trazar rutas de aprendizaje personalizadas y garantizar un seguimiento evolutivo basado en evidencias, facilitando así una intervención docente más asertiva y centrada en el desarrollo individual.',
            'token' => bin2hex(random_bytes(4)),
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update Questions
        DB::table('diag_questions')
            ->update(['diag_main_id' => $diagMainId]);

        // Update Sessions
        DB::table('diag_sessions')
            ->update(['diag_main_id' => $diagMainId]);
    }

    public function down()
    {
        // Optional: clear diag_main_id and delete mains created by migration?
        // Since we didn't track exactly which ones we created (unless we query by name), 
        // a safe rollback might just be setting columns null (which happens in the column migrations).

        // Let's just try to revert the specific logic if needed, but usually data migrations are hard to reverse cleanly without data loss.
        // We will nullify the columns.
        DB::table('diag_questions')->update(['diag_main_id' => null]);
        DB::table('diag_sessions')->update(['diag_main_id' => null]);

        // Delete the auto-generated mains
        DB::table('diag_mains')
            ->where('name', 'Diagnóstico Educativo. Aproximación pedagogica y exploración de Competencias')
            ->delete();
    }
}
