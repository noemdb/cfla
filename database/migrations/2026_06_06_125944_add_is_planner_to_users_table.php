<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'is_planner')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_planner')->default(false)->after('is_diagnostic');
                $table->index('is_planner');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'is_planner')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['is_planner']);
                $table->dropColumn('is_planner');
            });
        }
    }
};
