<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusQuotaToMailers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mailers', function (Blueprint $table) {
            $table->boolean('status_quota')->nullable()->default(false)->after('status_test')->comment('Filtrado por cuotas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mailers', function (Blueprint $table) {
            $table->dropColumn('status_quota');
        });
    }
}
