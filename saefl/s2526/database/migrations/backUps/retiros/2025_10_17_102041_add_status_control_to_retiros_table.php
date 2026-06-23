<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusControlToRetirosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retiros', function (Blueprint $table) {
            $table->enum('status_control',['true','false'])->after('status_admon')->default('false')->comment('Retiro Control de Estudios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retiros', function (Blueprint $table) {
            $table->dropColumn('status_control');
        });
    }
}
