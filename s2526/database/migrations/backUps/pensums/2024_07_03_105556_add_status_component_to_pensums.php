<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusComponentToPensums extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pensums', function (Blueprint $table) {
            $table->enum('status_component',['true','false'])->after('asignatura_id')->default('false')->comment('Contiene Componentes de Formación?');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pensums', function (Blueprint $table) {
            $table->dropColumn('status_component');
        });
    }
}
