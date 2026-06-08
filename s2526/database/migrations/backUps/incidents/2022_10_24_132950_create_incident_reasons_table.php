<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateIncidentReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incident_reasons', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name')->comment('Nombre');
            $table->timestamps();
        });

        $data = array(
            array('name'=>'No cumplió con las actividades de trabajo en clase'),
            array('name'=>'No presto atención en clase'),
            array('name'=>'Faltó al respeto a sus compañeros o docente'),
            array('name'=>'Participó en una pelea'),
            array('name'=>'Interfirió en el aprendizaje de sus compañeros'),
            array('name'=>'Destruyó materiales de la institución'),
            array('name'=>'Constante conducta agresiva'),
            array('name'=>'Habló mucho en clase'),
            array('name'=>'Dijo malas palabras'),
            array('name'=>'Usó un lenguaje obseno'),
            array('name'=>'Conducta peligrosa durante el receso'),
            array('name'=>'Mal comportamiento durante el receso')
        );

        DB::table('incident_reasons')->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incident_reasons');
    }
}

/*

TRUNCATE `s2223`.`incident_reasons`;
INSERT INTO `incident_reasons` (`id`, `name`, `created_at`, `updated_at`) VALUES 
(NULL, 'No cumplió con las actividades de trabajo en clase', NULL, NULL), 
(NULL, 'No presto atención en clase', NULL, NULL), 
(NULL, 'Faltó al respeto a sus compañeros o docente', NULL, NULL), 
(NULL, 'Participó en una pelea', NULL, NULL),
(NULL, 'Interfirió en el aprendizaje de sus compañeros', NULL, NULL), 
(NULL, 'Destruyó materiales de la institución', NULL, NULL),
(NULL, 'Constante conducta agresiva', NULL, NULL),
(NULL, 'Habló mucho en clase', NULL, NULL), 
(NULL, 'Dijo malas palabras', NULL, NULL),
(NULL, 'Usó un lenguaje obseno', NULL, NULL), 
(NULL, 'Conducta peligrosa durante el receso', NULL, NULL), 
(NULL, 'Mal comportamiento durante el receso', NULL, NULL);

INSERT INTO `incident_reasons` (`id`, `name`, `created_at`, `updated_at`) VALUES (NULL, 'Incumplió con la normativa y/o acuerdo de convivencia de la institución', NULL, NULL);
INSERT INTO `incident_reasons` (`id`, `name`, `created_at`, `updated_at`) VALUES (NULL, 'Otro', NULL, NULL);

*/