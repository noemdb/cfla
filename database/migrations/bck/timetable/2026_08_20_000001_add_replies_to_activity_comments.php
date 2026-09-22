<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * SPEC REPLIES-COMMENTS-001 (blueprint/comments/replicas-comentarios.md).
 *
 * Réplicas del profesor a comentarios de estudiantes (LMS):
 *  - parent_id: self-FK nullable → parent_id NULL = comentario raíz;
 *    parent_id no nulo = réplica directa (profundidad 2 niveles, ADR-003).
 *  - is_instructor_reply: flag para distinguir réplicas creadas por un
 *    moderador (autoaprobadas) de los comentarios raíz del estudiante.
 *
 * Idempotente: no-op si las columnas ya existen.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_comments', function (Blueprint $table) {
            if (! Schema::hasColumn('activity_comments', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('activity_id');
                $table->foreign('parent_id')
                    ->references('id')->on('activity_comments')
                    ->onDelete('cascade');
            }

            if (! Schema::hasColumn('activity_comments', 'is_instructor_reply')) {
                $table->boolean('is_instructor_reply')->default(false)->after('parent_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('activity_comments', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'is_instructor_reply']);
        });
    }
};
