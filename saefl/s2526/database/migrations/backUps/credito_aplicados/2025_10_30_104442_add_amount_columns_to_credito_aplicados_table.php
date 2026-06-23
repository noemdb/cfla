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
        Schema::table('credito_aplicados', function (Blueprint $table) {
            $table->decimal('ammount_applied', 15, 2)->default(0)->after('credito_aplicado_observations');
            $table->decimal('exchange_ammount_applied', 15, 2)->default(0)->after('ammount_applied');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credito_aplicados', function (Blueprint $table) {
            $table->dropColumn(['ammount_applied', 'exchange_ammount_applied']);
        });
    }
};