<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAiPromptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('ai_prompts', function (Blueprint $table) {
            $table->id();
            $table->enum('prompt_type', ['system', 'user'])->comment('system, user');
            $table->string('name');
            $table->string('version')->comment('e.g. 1.0, 1.1');
            $table->text('content')->comment('The prompt text. IMMUTABLE.');
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);

            // Users table uses increments (unsignedInteger), not BigIncrements.
            $table->unsignedInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ai_prompts');
    }
}
