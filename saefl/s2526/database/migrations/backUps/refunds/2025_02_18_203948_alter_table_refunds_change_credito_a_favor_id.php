<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableRefundsChangeCreditoAFavorId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('refunds', function (Blueprint $table) {
            $table->bigInteger('credito_a_favor_id')->unsigned()->nullable()->comment('Credito a Favor')->change(); // Change the column to nullable
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('refunds', function (Blueprint $table) {
            $table->bigInteger('credito_a_favor_id')->unsigned()->comment('Credito a Favor')->change();
        });
    }
}
