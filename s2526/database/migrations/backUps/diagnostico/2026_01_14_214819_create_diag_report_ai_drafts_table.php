<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiagReportAiDraftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('diag_report_ai_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('diag_reports')->onDelete('cascade');

            $table->string('llm_provider')->comment('openai, gemini, local');
            $table->string('llm_model');

            $table->foreignId('system_prompt_id')->constrained('ai_prompts');
            $table->foreignId('user_prompt_id')->constrained('ai_prompts');
            $table->string('prompt_version_label')->comment('Snapshot of Prompt IDs/Versions');

            $table->string('input_hash')->comment('SHA256 of the JSON payload');
            $table->longText('output_text')->nullable()->comment('The AI generated content');

            $table->string('status')->default('generated')->comment('generated, edited, approved');

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
        Schema::dropIfExists('diag_report_ai_drafts');
    }
}
