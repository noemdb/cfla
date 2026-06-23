<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddManagerIdToPestudios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pestudios', function (Blueprint $table) {
            $table->unsignedInteger('manager_id')->after('code')->nullable()->comment('Usuario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pestudios', function (Blueprint $table) {
            $table->dropColumn('manager_id');
        });
    }
}
