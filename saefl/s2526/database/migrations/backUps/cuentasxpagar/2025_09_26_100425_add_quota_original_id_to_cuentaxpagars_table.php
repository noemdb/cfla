<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuotaOriginalIdToCuentaxpagarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cuentaxpagars', function (Blueprint $table) {
            if (!Schema::hasColumn('cuentaxpagars', 'quota_original_id')) {
                $table->unsignedBigInteger('quota_original_id')->nullable()->after('planpago_id');
                $table->unique(['estudiant_id', 'quota_original_id']);
            }
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cuentaxpagars', function (Blueprint $table) {
            //
        });
    }
}
