<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateIncidentFaultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incident_faults', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('duty_id')->unsigned();
            $table->text('description')->nullable();
            $table->boolean('status_active')->default(true);
            $table->timestamps();
            $table->foreign('duty_id')->references('id')->on('incident_duties')->onDelete('cascade')->onUpdate('cascade');
        });

        if (!DB::table('incident_faults')->exists()) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('incident_faults')->truncate();
            $datas = [
                [ 'duty_id' => 1,'description' => "Gorra, lazos o cintillos extravagantes, medias tobilleras y cinturones de colores, no portar el distintivo, collares, pulseras, anillos extravagantes, zapatos, correas, abrigo y todo aquello relacionado al uniforme."],
                [ 'duty_id' => 2,'description' => "Salir del colegio sin pase y sin que el representante lo retire. Inasistencias reiteradas sin justificación por parte del representante."],
                [ 'duty_id' => 3,'description' => "Empujar, pegar, decir groserías, punta pie, patada, jalones de pelo, dañar los útiles de sus compañeros."],
                [ 'duty_id' => 4,'description' => "Dañar los bienes pertenecientes a la institución (mesas, sillas, pizarras, carteleras, pupitres lavamanos, pocetas, paredes, puertas, equipos, útiles escolares de sus compañeros, entre otros.)"],
                [ 'duty_id' => 5,'description' => "Celular encendido durante la jornada escolar. Tomar fotos, grabar y reproducir videos o cualquier material multimedia de sus compañeros, profesores o cualquier persona de la institución con objetos de burla dentro o adyacencias de la comunidad escolar que atente psicológica, física y moralmente la integridad de los niños, niñas y adolescentes (sticker, fotos, videos, memes amenazantes). Mostrarse en las redes sociales con conductas inapropiadas, llevando el uniforme del colegio o algún elemento que lo identifique."],
                [ 'duty_id' => 6,'description' => "Participar o incitar en peleas u otro tipo de violencia, tanto de palabra (burlas, sobrenombres, chalequeo) como de hecho (golpes, empujones, tirar del cabello, patadas, escupitajos) dentro del plantel o sus alrededores en contra de los estudiantes. Incitar al odio."],
                [ 'duty_id' => 7,'description' => "Manifestaciones de noviazgo dentro del plantel o sus adyacencias, con o sin uniforme escolar, tales como besos, roces, andar agarrados de la mano o esconderse en algún sitio del Colegio. Tocar de forma intencional los genitales o partes íntimas de algún integrante de la Comunidad Educativa del plantel en los baños u otras áreas de la Institución."],
                [ 'duty_id' => 8,'description' => "Ingresar a la institución educativa o sustancias ilícitas (licor, vaper, cigarrillos, bebidas energéticas."],
                [ 'duty_id' => 9,'description' => "Durante el tiempo de receso permanecer en las áreas no permitidas o restringidas por el plantel (canchas de primaria, jardín del privado, detrás de la cantina)"],
            ] ;
            DB::table('incident_faults')->insert($datas);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incident_faults');
    }
}
