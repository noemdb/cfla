<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCatchmentActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catchment_activities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->nullable()->unsigned()->comment('Grupo');
            $table->string('name')->comment("Nombre");
            $table->text('description')->nullable()->comment("Descripción");
            $table->date('date')->comment("Fecha de la actividad");
            $table->time('time')->comment("Hora de la actividad");
            $table->boolean('status_active')->default(true);
            $table->timestamps();
        });

        if (!DB::table('catchment_activities')->exists()) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('catchment_activities')->truncate();
            $datas = [
                [ 'group_id'=>1,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-02-01','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>1,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-02-02','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>1,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-02-03','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>2,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-02-04','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>2,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-02-05','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>2,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-02-06','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>3,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-02-07','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>3,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-02-08','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>3,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-02-09','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>4,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-02-10','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>4,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-02-11','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>4,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-02-12','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>5,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-02-13','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>5,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-02-14','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>5,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-02-15','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>6,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-02-16','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>6,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-02-17','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>6,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-02-18','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>7,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-02-19','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>7,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-02-20','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>7,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-02-21','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>8,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-02-22','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>8,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-02-23','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>8,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-02-24','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>9,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-02-25','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>9,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-02-26','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>9,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-02-27','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>10,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-02-28','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>10,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-02-01','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>10,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-02-02','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>11,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-02-03','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>11,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-02-04','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>11,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-02-05','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>12,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-02-06','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>12,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-02-07','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>12,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-02-08','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>12,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-03-09','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>12,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-03-10','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>12,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-03-11','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>13,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-03-12','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>13,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-03-13','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>13,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-03-14','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>14,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-03-15','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>14,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-03-16','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>14,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-03-17','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>15,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-03-18','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>15,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-03-19','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>15,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-03-20','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>16,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-03-21','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>16,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-03-22','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>16,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-03-23','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>17,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-02-24','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>17,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-02-25','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>17,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-02-26','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>18,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-02-27','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>18,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-02-28','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>18,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-02-01','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>19,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-02-02','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>19,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-02-03','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>19,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-02-04','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>20,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-02-05','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>20,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-02-06','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>20,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-02-07','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>21,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-02-08','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>21,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-02-09','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>21,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-02-10','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>21,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-02-11','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>21,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-02-12','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>21,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-02-13','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>22,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-02-14','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>22,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-02-15','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>22,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-02-16','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>23,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-02-17','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>23,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-02-18','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>23,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-02-19','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>24,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-02-20','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>24,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-02-21','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>24,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-02-22','time'=>'08:00:00','status_active'=>true],

                [ 'group_id'=>25,'name'=>"Actividad A", 'description'=>"1ra Actividad: presentanción, integración y avance",'date'=> '2023-02-23','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>25,'name'=>"Actividad B", 'description'=>"2da Actividad: Eventos, alegría y conocimiento",'date'=> '2023-02-24','time'=>'08:00:00','status_active'=>true],
                [ 'group_id'=>25,'name'=>"Actividad C", 'description'=>"3ra Actividad: Compromiso y válidez",'date'=> '2023-02-25','time'=>'08:00:00','status_active'=>true],

            ] ;
            DB::table('catchment_activities')->insert($datas);
        } 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catchment_activities');
    }
}
