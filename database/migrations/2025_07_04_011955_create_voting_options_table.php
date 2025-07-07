<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voting_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('poll_id');
            $table->string('label');
            $table->integer('votes_count')->default(0);
            $table->timestamps();

            $table->foreign('poll_id')->references('id')->on('voting_polls')->onDelete('cascade');
            $table->index(['poll_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voting_options');
    }
};
