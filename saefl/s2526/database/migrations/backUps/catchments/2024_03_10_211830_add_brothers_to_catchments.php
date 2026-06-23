<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBrothersToCatchments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('catchments', function (Blueprint $table) {
            $table->float('brothers',2,0)->after('status_siblings_college')->default(1)->comment('Cantidad de hermanos(a) en el colegio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catchments', function (Blueprint $table) {
            $table->dropColumn('brothers');
        });
    }
}
