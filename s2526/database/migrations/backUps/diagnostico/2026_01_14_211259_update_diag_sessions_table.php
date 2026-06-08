<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDiagSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('diag_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('diag_sessions', 'lapso_id')) {
                $table->unsignedInteger('lapso_id')->nullable();
                $table->foreign('lapso_id')->references('id')->on('lapsos');
            }
            if (!Schema::hasColumn('diag_sessions', 'status')) {
                $table->string('status')->default('draft')->comment('draft, completed, cancelled, validated');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('diag_sessions', function (Blueprint $table) {
            $table->dropForeign(['lapso_id']);
            $table->dropColumn(['lapso_id', 'status']);
        });
    }
}
