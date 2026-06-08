<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDiagQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('diag_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('diag_questions', 'competency_id')) {
                $table->foreignId('competency_id')->nullable()->constrained('diag_competencies');
            }
            if (!Schema::hasColumn('diag_questions', 'indicator_id')) {
                $table->foreignId('indicator_id')->nullable()->constrained('diag_indicators');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diag_questions', function (Blueprint $table) {
            $table->dropForeign(['competency_id']);
            $table->dropForeign(['indicator_id']);

            $table->dropColumn(['competency_id', 'indicator_id']);
        });
    }
}
