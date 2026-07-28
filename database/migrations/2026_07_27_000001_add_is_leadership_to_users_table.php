<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'is_leadership')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_leadership')
                    ->default(false)
                    ->after('is_profesor')
                    ->comment('Jefe de área con capacidad de seguimiento');
                $table->index('is_leadership');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'is_leadership')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['is_leadership']);
                $table->dropColumn('is_leadership');
            });
        }
    }
};
