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
        Schema::create('voting_polls', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('access_token')->unique();
            $table->boolean('enable')->default(false);
            $table->dateTime('date')->nullable(); // inicio
            $table->integer('time_active'); // duración en minutos
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voting_polls');
    }
};
