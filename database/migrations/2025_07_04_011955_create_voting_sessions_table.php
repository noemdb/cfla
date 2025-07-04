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
        Schema::create('voting_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('ip')->nullable();
            $table->string('fingerprint')->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('voted')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->unsignedBigInteger('poll_id');
            $table->timestamps();

            $table->foreign('poll_id')->references('id')->on('voting_polls')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voting_sessions');
    }
};
