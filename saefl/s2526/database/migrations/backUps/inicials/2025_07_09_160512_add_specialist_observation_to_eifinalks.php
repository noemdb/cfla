<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSpecialistObservationToEifinalks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eifinalks', function (Blueprint $table) {
            $table->text('specialist_observation')->after('individual_observations')->nullable()->comment('Observación de los Especialistas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('eifinalks', function (Blueprint $table) {
            $table->dropColumn('specialist_observation');
        });
    }
}
