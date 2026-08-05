<?php
// database/migrations/2026_08_04_000001_add_is_director_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'is_director')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_director')
                    ->default(false)
                    ->after('is_leadership')
                    ->comment('Dirección: supervisión y seguimiento de solo lectura');
                $table->index('is_director');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'is_director')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['is_director']);
                $table->dropColumn('is_director');
            });
        }
    }
};
