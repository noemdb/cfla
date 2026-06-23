<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddObservationsUserToCreditoAFavorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credito_a_favors', function (Blueprint $table) {
            $table->string('observations_user')->after('credito_a_favor_ids')->nullable()->comment('password sender');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('credito_a_favors', function (Blueprint $table) {
            $table->dropColumn('observations_user');
        });
    }
}
