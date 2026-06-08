<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToBoletinRevisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boletin_revisions', function (Blueprint $table) {
            $table->string('type')->nullable()->default('REVISION')->after('estudiant_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boletin_revisions', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
