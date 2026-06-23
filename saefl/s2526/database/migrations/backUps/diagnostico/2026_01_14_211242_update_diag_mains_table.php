<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDiagMainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('diag_mains', function (Blueprint $table) {
            if (!Schema::hasColumn('diag_mains', 'referent_id')) {
                $table->foreignId('referent_id')->nullable()->constrained('diag_referents');
            }

            // lapsos and pestudios utilize increments() which is unsignedInteger, not BigInteger
            if (!Schema::hasColumn('diag_mains', 'lapso_id')) {
                $table->unsignedInteger('lapso_id')->nullable();
                $table->foreign('lapso_id')->references('id')->on('lapsos');
            }

            if (!Schema::hasColumn('diag_mains', 'pestudio_id')) {
                $table->unsignedInteger('pestudio_id')->nullable();
                $table->foreign('pestudio_id')->references('id')->on('pestudios');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diag_mains', function (Blueprint $table) {
            $table->dropForeign(['referent_id']);
            $table->dropForeign(['lapso_id']);
            $table->dropForeign(['pestudio_id']);

            $table->dropColumn(['referent_id', 'lapso_id', 'pestudio_id']);
        });
    }
}
