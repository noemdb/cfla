<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrackingFieldsToDiagRecommendationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('diag_recommendations', function (Blueprint $table) {
            $table->unsignedInteger('assigned_to')->nullable()->after('active');
            $table->foreign('assigned_to')->references('id')->on('users');

            $table->timestamp('started_at')->nullable()->after('assigned_to');
            $table->timestamp('completed_at')->nullable()->after('started_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('diag_recommendations', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn(['assigned_to', 'started_at', 'completed_at']);
        });
    }
}
