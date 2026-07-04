<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssistantIdToPeducativosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('peducativos', function (Blueprint $table) {
            $table->unsignedInteger('assistant_id')->after('manager_id')->nullable()->comment('Asistente');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('peducativos', function (Blueprint $table) {
            $table->dropColumn('assistant_id');
        });
    }
}
