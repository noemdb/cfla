<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('activity_comments')) {
            Schema::create('activity_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('activity_id')
                    ->constrained('activities')
                    ->cascadeOnDelete();
                $table->unsignedInteger('user_id');
                $table->foreign('user_id')
                    ->references('id')->on('users')
                    ->cascadeOnDelete();
                $table->text('body');
                $table->boolean('is_approved')->default(false);
                $table->timestamp('approved_at')->nullable();
                $table->unsignedInteger('approved_by')->nullable();
                $table->foreign('approved_by')
                    ->references('id')->on('users');
                $table->timestamps();
                $table->softDeletes();

                $table->index(['activity_id', 'is_approved', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_comments');
    }
};
