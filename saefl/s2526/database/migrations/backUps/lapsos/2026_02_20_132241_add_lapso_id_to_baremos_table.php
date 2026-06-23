<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLapsoIdToBaremosTable extends Migration
{
    public function up()
    {
        Schema::table('baremos', function (Blueprint $table) {
            // ⚠️ USAR unsignedInteger PARA COINCIDIR CON lapsos.id
            $table->unsignedInteger('lapso_id')->nullable()->after('pestudio_id');
            
            // Agregar índice
            $table->index('lapso_id', 'baremos_lapso_id_index');
            
            // Agregar foreign key
            $table->foreign('lapso_id', 'baremos_lapso_id_foreign')
                  ->references('id')
                  ->on('lapsos')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('baremos', function (Blueprint $table) {
            $table->dropForeign('baremos_lapso_id_foreign');
            $table->dropIndex('baremos_lapso_id_index');
            $table->dropColumn('lapso_id');
        });
    }
}
