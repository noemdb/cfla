<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voting_polls', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('access_token', 32)->unique();
            $table->boolean('enable')->default(false);
            $table->timestamp('date')->nullable();
            $table->integer('time_active')->default(60); // minutos
            $table->timestamps();

            $table->index(['enable', 'date']);
            $table->index('access_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voting_polls');
    }
};
