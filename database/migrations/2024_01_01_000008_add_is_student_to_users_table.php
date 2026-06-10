<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'is_student')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_student')->default(false)->after('is_profesor');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'is_student')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_student');
            });
        }
    }
};
