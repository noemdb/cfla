<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Flag de rol para el módulo de Educación Inicial (pestudio 6).
 *
 * El legacy resolvía el acceso al módulo con la tabla `rols` (área/rol/vigencia)
 * y 4 checks `Is{Inicial,Evaluacion,Planning,Academico}` sobre el User, con una
 * superposición excesiva: cualquier usuario con rol SISTEMA/ADMINISTRADOR
 * pasaba los cuatro. cfla usa flags booleanos planos, así que este es el único
 * flag que hay que añadir — `is_profesor`, `is_planner`, `is_diagnostic` e
 * `is_admin` ya existen.
 *
 * Blueprint: blueprint/inicial · decisión D2 · fase F1.
 *
 * ─────────────────────────────────────────────────────────────────
 * ─────────────────────────────────────────────────────────────────
 * MIGRACIÓN ADITIVA — YA APLICADA
 * ─────────────────────────────────────────────────────────────────
 * El proyecto está en producción (`s2627`) y tiene 3.439 usuarios, por eso se
 * pidió confirmación explícita antes de correrla. Queda registrada en la tabla
 * `migrations` (batch 27): `users.is_inicial` existe y
 * `IsInicial::migracionPendiente()` devuelve `false`.
 *
 * Los guardas `Schema::hasColumn('users','is_inicial')` de
 * `Admin\Users\IndexComponent` y `Planning\Profesor\IndexComponent` NO se
 * quitan: siguen siendo necesarios en entornos que no la tengan (clon recién
 * bajado, base de pruebas, otro año escolar).
 *
 * Se declara NOT NULL DEFAULT 0 (igual que `is_admin`, `is_profesor` y
 * `is_coordinacion`) para que la columna pueda indexarse y no genere NULLs en
 * las 3.439 filas existentes. Ojo: `is_diagnostic` es la excepción del
 * esquema — la única nullable — y por eso NO se usa como patrón aquí.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'is_inicial')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_inicial')
                ->default(false)
                ->after('is_diagnostic')
                ->comment('Acceso al módulo de Educación Inicial (pestudio 6)');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'is_inicial')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_inicial');
        });
    }
};
