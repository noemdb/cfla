<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterTableIncidentsChangeType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incidents', function (Blueprint $table) {
            // $table->enum('type',['Académica','Disciplinaria','Académica y disciplinaria','Otro'])->change();
            DB::statement("ALTER TABLE `incidents` CHANGE `type` `type` ENUM('Académica','Disciplinaria','Académica y disciplinaria','Otro')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incidents', function (Blueprint $table) {
            // $table->enum('type',['Académica','Disciplinaria','Otro'])->change();
            DB::statement("ALTER TABLE `incidents` CHANGE `type` `type` ENUM('Académica','Disciplinaria','Otro')");
        });
    }
}
