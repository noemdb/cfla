<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusBadToCuentaxpagarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cuentaxpagars', function (Blueprint $table) {
            $table->enum('status_bad',['true','false'])->after('status_exchange')->default('false')->comment('Cuenta incobrable');
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
            $table->dropColumn('status_bad');
        });
    }
}
