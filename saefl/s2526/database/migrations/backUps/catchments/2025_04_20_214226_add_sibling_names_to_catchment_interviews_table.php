<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSiblingNamesToCatchmentInterviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('catchment_interviews', function (Blueprint $table) {
            $table->string('sibling_name_2')->nullable()->after('sibling_name')->comment('Nombre del segundo hermano/a');
            $table->string('sibling_name_3')->nullable()->after('sibling_name_2')->comment('Nombre del tercer hermano/a');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catchment_interviews', function (Blueprint $table) {
            $table->dropColumn(['sibling_name_2', 'sibling_name_3']);
        });
    }
}
