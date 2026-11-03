<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Perfil de seguimiento de Educación Inicial (solo lectura).
 *
 * Blueprint: blueprint/inicial · doc 11 · ADR-11.1 / ADR-11.10.
 *
 * Una sola columna booleana. No crea tablas ni modifica datos existentes:
 * todos los usuarios quedan con `false` y conservan su comportamiento actual.
 *
 * El guard `hasColumn` permite volver a ejecutar la migración en entornos
 * donde la columna ya se añadió a mano (mismo patrón que
 * bck/users/2026_10_07_000001_add_is_inicial_to_users_table.php).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'is_inicial_manager')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('is_inicial_manager')
                ->default(false)
                ->after('is_inicial');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'is_inicial_manager')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('is_inicial_manager');
        });
    }
};
