<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusRepresentantToPollMainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('poll_mains', function (Blueprint $table) {
            $table->enum('status_representant',['true','false'])->after('status_estudiant')->default('false')->comment('Excluye a representante');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('poll_mains', function (Blueprint $table) {
            $table->dropColumn('status_representant');
        });
    }
}
