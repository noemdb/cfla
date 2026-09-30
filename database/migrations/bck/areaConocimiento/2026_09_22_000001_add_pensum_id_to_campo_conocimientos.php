<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campo_conocimientos', function (Blueprint $table): void {
            $table->unsignedBigInteger('pensum_id')->nullable()->after('asignatura_id');
            $table->foreign('pensum_id')->references('id')->on('pensums')->nullOnDelete();
            $table->index('pensum_id');
        });
    }

    public function down(): void
    {
        Schema::table('campo_conocimientos', function (Blueprint $table): void {
            $table->dropForeign(['pensum_id']);
            $table->dropIndex(['pensum_id']);
            $table->dropColumn('pensum_id');
        });
    }
};
