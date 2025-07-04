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
        Schema::create('voting_votes', function (Blueprint $table) {
            $table->id();
            $table->uuid('session_uuid');
            $table->unsignedBigInteger('option_id');
            $table->timestamps();

            $table->foreign('session_uuid')->references('uuid')->on('voting_sessions');
            $table->foreign('option_id')->references('id')->on('voting_options')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voting_votes');
    }
};
