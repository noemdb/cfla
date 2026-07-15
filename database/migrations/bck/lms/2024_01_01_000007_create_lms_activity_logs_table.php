<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->restrictOnDelete();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
            $table->enum('event', [
                'VIEW', 'CONTENT_VIEW', 'RESOURCE_DOWNLOAD',
                'PUBLISH', 'UNPUBLISH', 'EDIT', 'SECTION_ADD',
                'RESOURCE_ADD', 'RESOURCE_DELETE',
            ]);
            $table->unsignedBigInteger('context_id')->nullable();
            $table->string('context_type', 80)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['activity_id', 'event', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_activity_logs');
    }
};
