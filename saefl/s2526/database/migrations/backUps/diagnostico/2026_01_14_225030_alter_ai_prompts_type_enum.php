<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterAiPromptsTypeEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Using DB statement for enum change to avoid doctrine/dbal complexities with enums
        DB::statement("ALTER TABLE ai_prompts MODIFY COLUMN prompt_type ENUM('system', 'user') NOT NULL COMMENT 'system, user'");
    }

    public function down()
    {
        Schema::table('ai_prompts', function (Blueprint $table) {
            $table->string('prompt_type')->change();
        });
    }
}
