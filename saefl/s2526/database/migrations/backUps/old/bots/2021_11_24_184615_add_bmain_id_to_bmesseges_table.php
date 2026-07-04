<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBmainIdToBmessegesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bmesseges', function (Blueprint $table) {
            $table->unsignedSmallInteger('bmain_id')->after('id')->nullable()->comment('Autorrespondedor');
            $table->foreign('bmain_id')->references('id')->on('bmains')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bmesseges', function (Blueprint $table) {
            $table->dropForeign('bmain_id');
        });
    }
}
