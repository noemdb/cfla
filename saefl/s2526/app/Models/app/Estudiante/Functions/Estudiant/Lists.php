<?php
namespace App\Models\app\Estudiante\Functions\Estudiant;

use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Pestudio;
use Illuminate\Support\Facades\DB;

trait Lists {

    public static function list_country_birth() /* usada para llenar los objetos de formularios select*/
    {
        $list = [
        'VENEZUELA'=>'VENEZUELA',
        'ALEMANIA'=>'ALEMANIA',
        'ARABIA SAUDITA'=>'ARABIA SAUDITA',
        'ARGENTINA'=>'ARGENTINA',
        'ARUBA'=>'ARUBA',
        'AUSTRALIA'=>'AUSTRALIA',
        'BELGICA'=>'BELGICA',
        'BOLIVIA'=>'BOLIVIA',
        'BRASIL'=>'BRASIL',
        'CANADA'=>'CANADA',
        'CHILE'=>'CHILE',
        'CHINA'=>'CHINA',
        'COLOMBIA'=>'COLOMBIA',
        'COSTA RICA'=>'COSTA RICA',
        'CUBA'=>'CUBA',
        'CURAZAO'=>'CURAZAO',
        'DINAMARCA'=>'DINAMARCA',
        'ECUADOR'=>'ECUADOR',
        'EL SALVADOR'=>'EL SALVADOR',
        'EMIRATOS ARABES UNIDOS'=>'EMIRATOS ARABES UNIDOS',
        'ESPAÑA'=>'ESPAÑA',
        'ESTADOS UNIDOS DE NORTEAMERICA'=>'ESTADOS UNIDOS DE NORTEAMERICA',
        'FILIPINAS'=>'FILIPINAS',
        'FRANCIA'=>'FRANCIA',
        'GRAN BRETAÑA E IRLANDA'=>'GRAN BRETAÑA E IRLANDA',
        'GUATEMALA'=>'GUATEMALA',
        'GUYANA'=>'GUYANA',
        'HAITI'=>'HAITI',
        'HOLANDA'=>'HOLANDA',
        'INGLATERRA'=>'INGLATERRA',
        'IRAQ'=>'IRAQ',
        'ISLAS CANARIAS'=>'ISLAS CANARIAS',
        'ITALIA'=>'ITALIA',
        'JAPON'=>'JAPON',
        'JORDANIA'=>'JORDANIA',
        'KUWAIT'=>'KUWAIT',
        'LA ROMANA'=>'LA ROMANA',
        'LIBANO'=>'LIBANO',
        'LITUANIA'=>'LITUANIA',
        'MARRUECOS'=>'MARRUECOS',
        'MEXICO'=>'MEXICO',
        'MIAMI'=>'MIAMI',
        'NICARAGUA'=>'NICARAGUA',
        'NORUEGA'=>'NORUEGA',
        'ONTARIO, KANADA'=>'ONTARIO, KANADA',
        'PAISES BAJOS'=>'PAISES BAJOS',
        'PANAMÁ'=>'PANAMÁ',
        'PERÚ'=>'PERÚ',
        'POLONIA'=>'POLONIA',
        'PORTUGAL'=>'PORTUGAL',
        'PUERTO RICO'=>'PUERTO RICO',
        'QATAR'=>'QATAR',
        'REINO UNIDO'=>'REINO UNIDO',
        'REPÚBLICA CHECA'=>'REPÚBLICA CHECA',
        'REPUBLICA DOMINICANA'=>'REPUBLICA DOMINICANA',
        'ROMA'=>'ROMA',
        'RUSIA'=>'RUSIA',
        'SIRIA'=>'SIRIA',
        'SUIZA'=>'SUIZA',
        'TAILANDIA'=>'TAILANDIA',
        'TRINIDAD Y TOBAGO'=>'TRINIDAD Y TOBAGO',
        'UCRANIA'=>'UCRANIA',
        'URUGUAY'=>'URUGUAY',
        'VICENZA'=>'VICENZA',
        'OTRO'=>'OTRO',
        ];
        return $list;
    }

    public static function list_pestudio_grado() /* usada para llenar los objetos de formularios select*/
    {
        $pestudios = Pestudio::active('true')->get();

        $datas_estudiants = collect();

        foreach ($pestudios as $pestudio) {
            $grados = $pestudio->grados;
            $datas_grado = collect();
            foreach ($grados as $grado) {
                $estudiants_pluck = $grado->estudiants->pluck('ci_fullname','id');
                $datas_grado->put($grado->name, $estudiants_pluck);
            }
            $datas_estudiants->put($pestudio->name, $datas_grado);
        }

        return $datas_estudiants;
    }
    public static function list_active() /* usada para llenar los objetos de formularios select*/
    {
        $estudiants = Estudiant::select('estudiants.ci_estudiant','estudiants.id',DB::raw('concat(estudiants.ci_estudiant, " ",estudiants.name," ",estudiants.lastname ) as cifullname'))
        ->active()
        ->orderBy('estudiants.ci_estudiant')
        // ->get()
        ->pluck('cifullname','id');

        return $estudiants;
    }

    public static function list() /* usada para llenar los objetos de formularios select*/
    {
        $estudiants = Estudiant::select('estudiants.ci_estudiant','estudiants.id',DB::raw('concat(estudiants.ci_estudiant, " ",estudiants.name," ",estudiants.lastname ) as cifullname'))
        ->orderBy('estudiants.ci_estudiant')
        // ->get()
        ->pluck('cifullname','id');

        return $estudiants;
    }

    public static function formalys($fecha=null) /* usada para llenar los objetos de formularios select*/
    {
        $estudiants = Estudiant::select('estudiants.*')
        ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
        ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
        ->whereNull('inscripcions.deleted_at');

        $estudiants = ($fecha) ? $estudiants->whereDate('inscripcions.created_at','<=',$fecha) : $estudiants ;

        $estudiants = $estudiants->get();

        return $estudiants;
    }

    public static function estudiantsPlanBeneficos($finicial=null,$ffinal=null) /* usada para llenar los objetos de formularios select*/
    {
        $estudiants = Estudiant::select('estudiants.*')
        ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
        ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
        ->join('plan_beneficos', 'estudiants.id', '=', 'plan_beneficos.estudiant_id')
        ->whereNull('inscripcions.deleted_at');

        $estudiants = ($finicial) ? $estudiants->whereDate('plan_beneficos.created_at','>=',$finicial) : $estudiants ;
        $estudiants = ($ffinal) ? $estudiants->whereDate('plan_beneficos.ffinal','>=',$ffinal) : $estudiants ;

        $estudiants = $estudiants->get();

        return $estudiants;
    }
}
