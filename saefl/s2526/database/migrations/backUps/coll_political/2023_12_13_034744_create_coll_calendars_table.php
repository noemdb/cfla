<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCollCalendarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coll_calendars', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallinteger('coll_political_id')->unsigned();
            $table->string('name')->comment('Nombre');
            $table->string('description')->nullable()->comment('Descripción');
            $table->date('date')->comment("Fecha");
            $table->time('time')->comment("Hora");
            // $table->integer('timestamp')->comment("Tiempo unix");
            $table->boolean('status_active')->default(true)->comment("Estado");
            $table->timestamps();
        });

        $datas = [
            [ 'coll_political_id' => 1,'name' => "1RA",'description' => "1RA notificación del mes actual",'date' => "2023-12-06",'time' => "10:00:00",'status_active' => true],
            [ 'coll_political_id' => 1,'name' => "2DA",'description' => "2DA notificación del mes actual",'date' => "2023-12-15",'time' => "10:00:00",'status_active' => true],
            [ 'coll_political_id' => 1,'name' => "3RA",'description' => "3RA notificación del mes actual",'date' => "2023-12-20",'time' => "10:00:00",'status_active' => true],
        ] ;
        DB::table('coll_calendars')->insert($datas);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coll_calendars');
    }
}
