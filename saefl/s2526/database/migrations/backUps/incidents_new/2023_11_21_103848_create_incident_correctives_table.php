<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateIncidentCorrectivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incident_correctives', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fault_id')->unsigned();
            $table->text('description');
            $table->boolean('status_active')->default(true);
            $table->timestamps();
            $table->foreign('fault_id')->references('id')->on('incident_faults')->onDelete('cascade')->onUpdate('cascade');
        });

        if (!DB::table('incident_correctives')->exists()) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('incident_correctives')->truncate();
            $datas = [
                ['fault_id' => 1,'description' => '1. El docente de aula citará al representante para realizar registro correspondiente.'],
                ['fault_id' => 1,'description' => '2. El representante junto a su estudiante realizará una charla sobre el uso adecuado del uniforme de cinco (5) a diez (10) minutos máximos.'],
                ['fault_id' => 1,'description' => '3. El estudiante en conjunto con su representen elaboraran: afiche, cartelera, papelógrafo, pendón con material de provecho sobre el uso adecuado del uniforme.'],
                ['fault_id' => 1,'description' => '4. Este proceso estará supervisado por el Coordinador del nivel.'],
                ///////////////////////////////////////////////////////////////////////////////////////
                ['fault_id' => 2,'description' => '1. El docente de aula citará al representante para realizar registro sobre la salida sin autorización del aula o de la institución de su representado.'],
                ['fault_id' => 2,'description' => '2. El docente de aula citará al representante dando un lapso de una (1) semana para justificar las inasistencias del escolar.'],
                ['fault_id' => 2,'description' => '3. El Docente registrará el resultado de la entrevista.'],
                ['fault_id' => 2,'description' => '4. Este proceso estará supervisado por el Coordinador del nivel.'],
                ///////////////////////////////////////////////////////////////////////////////////////
                ['fault_id' => 3,'description' => '1. El docente citará al representante para conversar sobre la situación de su representado y firmar acuerdo y compromisos para evitar la falta.'],
                ['fault_id' => 3,'description' => '2. El representante en conjunto con su representado elaborará una estrategia pedagógica orientada por el docente con relación al buen trato y convivencia escolar que se debe tener en la institución educativa.'],
                ['fault_id' => 3,'description' => '3. El estudiante en conjunto con su representante dictará una charla de cinco (5) minutos a diez (10) minutos, sobre los valores humanos intrínsecos en la institución educativa.'],
                ['fault_id' => 3,'description' => '4. El estudiante en conjunto con su representante deberá reparar o reponer los útiles escolares dañados en un lapso no mayor a quince (15) días.'],
                ['fault_id' => 3,'description' => '5. Este proceso estará supervisado por el Coordinador del nivel.'],
                ///////////////////////////////////////////////////////////////////////////////////////
                ['fault_id' => 4,'description' => '1. El docente de aula citará el represente para conversar sobre lo sucedido.'],
                ['fault_id' => 4,'description' => '2. El estudiante participará en la brigada de conservación del plantel.'],
                ['fault_id' => 4,'description' => '3. El docente de aula deberá citar al representante e informará el lapso establecido por el director(a) del plantel para la cancelación o reparación del daño.'],
                ['fault_id' => 4,'description' => '4. Este proceso estará supervisado por el Coordinador del nivel. '],
                ///////////////////////////////////////////////////////////////////////////////////////
                ['fault_id' => 5,'description' => '1. El docente de aula citará al representante para conversar y levantar el registro correspondiente de lo sucedido.'],
                ['fault_id' => 5,'description' => '2. El estudiante en conjunto de su representante realizará en el momento cívico una charla de cinco (5) a quince (15) minutos máximos con relación a los delitos cibernéticos.'],
                ['fault_id' => 5,'description' => '3. El estudiante en conjunto con su representante participará en la actividad de cuidado ecológico por un lapso escolar (solo un lapso).'],
                ['fault_id' => 5,'description' => '4. Este proceso estará supervisado por el Coordinador del nivel. '],
                ///////////////////////////////////////////////////////////////////////////////////////
                ['fault_id' => 6,'description' => '1. El docente tutor debe informar inmediatamente a los representantes de los estudiantes involucrados para hacer de su conocimiento el hecho; seguidamente, citará al representante para conversar sobre lo sucedido y llegar a acuerdos de resolución de conflictos.'],
                ['fault_id' => 6,'description' => '2. El docente tutor guiará al estudiante en conjunto con su representante en la realización de conversatorio sobre la Ley contra el Odio, Buen Trato o Valores Amigonianos.'],
                ['fault_id' => 6,'description' => '3. En caso de riñas, el docente tutor en conjunto con el Coordinador del Nivel remitirá el caso a dirección del plantel quien llevará al consejo de protección existente en el municipio.'],
                ['fault_id' => 6,'description' => '4. Este proceso estará supervisado por el Coordinador del nivel. '],
                ///////////////////////////////////////////////////////////////////////////////////////
                ['fault_id' => 7,'description' => '1. El docente tutor se reunirá con el representante para llegar acuerdos sobre lo sucedido.'],
                ['fault_id' => 7,'description' => '2. El estudiante dictará una charla o conversatorio junto al representante sobre sana sexualidad o Adolescencia responsable, ética y valor.'],
                ['fault_id' => 7,'description' => '3. Bajo la guía e instrucción del docente tutor, el estudiante participará en jornadas sobre proyecto de vida en el área de formación de orientación y convivencia.'],
                ['fault_id' => 7,'description' => '4. Este proceso estará supervisado por el Coordinador del nivel. '],
                ///////////////////////////////////////////////////////////////////////////////////////
                ['fault_id' => 8,'description' => '1. El docente tutor debe informar inmediatamente a los representantes de los estudiantes involucrados para hacer de su conocimiento el hecho; seguidamente, citará al representante para conversar sobre lo sucedido y llegar a acuerdos de resolución de conflictos.'],
                ['fault_id' => 8,'description' => '2. El docente tutor guiará al estudiante en conjunto con su representante en la realización de conversatorio sobre la Ley contra el Odio, Buen Trato o Valores Amigonianos.'],
                ['fault_id' => 8,'description' => '3. En caso de riñas, el docente tutor en conjunto con el Coordinador del Nivel remitirá el caso a dirección del plantel quien llevará al consejo de protección existente en el municipio.'],
                ['fault_id' => 8,'description' => '4. Este proceso estará supervisado por el Coordinador del nivel. '],
                ///////////////////////////////////////////////////////////////////////////////////////
                ['fault_id' => 8,'description' => '1. El docente tutor debe informar inmediatamente a los representantes de los estudiantes involucrados para hacer de su conocimiento el hecho; seguidamente, citará al representante para conversar sobre lo sucedido y llegar a acuerdos de resolución de conflictos.'],
                ['fault_id' => 8,'description' => '2. El docente tutor guiará al estudiante en conjunto con su representante en la realización de conversatorio sobre la Ley contra el Odio, Buen Trato o Valores Amigonianos.'],
                ['fault_id' => 8,'description' => '3. En caso de riñas, el docente tutor en conjunto con el Coordinador del Nivel remitirá el caso a dirección del plantel quien llevará al consejo de protección existente en el municipio.'],
                ['fault_id' => 8,'description' => '4. Este proceso estará supervisado por el Coordinador del nivel. '],
                ///////////////////////////////////////////////////////////////////////////////////////
                ['fault_id' => 9,'description' => '1. El docente tutor citará al representante para realizar registro respectivo'],
                ['fault_id' => 9,'description' => '2. El estudiante junto al padre o representante dictarán una charla, conversatorio, cartelera o juego didáctico sobre la importancia de mantenerse en los espacios permitidos o resguardados por el plantel.'],
                ['fault_id' => 9,'description' => '3. Este proceso estará supervisado por el Coordinador del nivel. '],
                ///////////////////////////////////////////////////////////////////////////////////////
            ]
            ;
            DB::table('incident_correctives')->insert($datas);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incident_correctives');
    }
}
