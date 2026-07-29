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
        Schema::table('activity_comments', function (Blueprint $table) {
            if (!Schema::hasColumn('activity_comments', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('approved_by');
                $table->unsignedInteger('rejected_by')->nullable()->after('rejected_at');
                $table->text('rejected_reason')->nullable()->after('rejected_by');

                $table->foreign('rejected_by')->references('id')->on('users');
            }
        });
    }

    public function down(): void
    {
        Schema::table('activity_comments', function (Blueprint $table) {
            $table->dropColumn(['rejected_at', 'rejected_by', 'rejected_reason']);
        });
    }
};
