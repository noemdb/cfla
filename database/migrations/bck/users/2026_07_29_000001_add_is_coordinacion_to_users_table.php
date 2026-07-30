<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'is_coordinacion')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_coordinacion')
                    ->default(false)
                    ->after('is_profesor')
                    ->comment('Coordinador de Programa Educativo');
                $table->index('is_coordinacion');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'is_coordinacion')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['is_coordinacion']);
                $table->dropColumn('is_coordinacion');
            });
        }
    }
};
