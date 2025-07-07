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
            $table->string('ip', 45)->nullable(); // IP pública
            $table->string('private_ip', 45)->nullable(); // IP privada de la red local
            $table->string('fingerprint', 64)->nullable(); // Hash SHA-256
            $table->text('user_agent')->nullable();
            $table->boolean('voted')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->unsignedBigInteger('poll_id');
            $table->timestamps();

            $table->foreign('poll_id')->references('id')->on('voting_polls')->onDelete('cascade');

            // Índice único combinando poll_id, fingerprint y private_ip para permitir múltiples dispositivos por IP pública
            $table->unique(['poll_id', 'fingerprint', 'private_ip'], 'unique_poll_device');

            // Índices para mejorar rendimiento
            $table->index(['poll_id', 'voted']);
            $table->index(['expires_at']);
            $table->index(['ip', 'private_ip']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voting_sessions');
    }
};
