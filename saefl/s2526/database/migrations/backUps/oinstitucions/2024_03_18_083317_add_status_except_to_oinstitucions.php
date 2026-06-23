<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusExceptToOinstitucions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oinstitucions', function (Blueprint $table) {
            $table->boolean('status_except')->nullable()->before('state')->default(false)->comment('Excluir de listados especiales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oinstitucions', function (Blueprint $table) {
            $table->dropColumn('status_except');
        });
    }
}
