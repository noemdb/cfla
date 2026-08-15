<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_lesson_reads', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('activity_id');
            $table->timestamp('read_at')->useCurrent();
            $table->unique(['user_id', 'activity_id']);

            // users.id es int(10) unsigned (no bigint): la FK debe coincidir.
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('activity_id')->references('id')->on('activities')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_lesson_reads');
    }
};
