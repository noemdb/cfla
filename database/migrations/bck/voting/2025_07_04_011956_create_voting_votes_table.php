<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voting_votes', function (Blueprint $table) {
            $table->id();
            $table->uuid('session_uuid');
            $table->unsignedBigInteger('option_id');
            $table->timestamps();

            $table->foreign('option_id')->references('id')->on('voting_options')->onDelete('cascade');
            // $table->unique('session_uuid');
            $table->index(['option_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voting_votes');
    }
};
