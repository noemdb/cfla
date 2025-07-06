<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voting_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('ip', 45)->nullable(); // Soporte para IPv6
            $table->string('fingerprint', 64)->nullable(); // Hash SHA-256
            $table->text('user_agent')->nullable();
            $table->boolean('voted')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->unsignedBigInteger('poll_id');
            $table->timestamps();

            $table->foreign('poll_id')->references('id')->on('voting_polls')->onDelete('cascade');

            // Índices únicos para prevenir votos duplicados
            $table->unique(['poll_id', 'fingerprint'], 'unique_poll_fingerprint');
            $table->unique(['poll_id', 'ip'], 'unique_poll_ip');

            // Índices para mejorar rendimiento
            $table->index(['poll_id', 'voted']);
            $table->index(['expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voting_sessions');
    }
};
