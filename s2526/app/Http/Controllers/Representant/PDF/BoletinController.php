<?php

namespace App\Http\Controllers\Representant\PDF;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

use App\Http\Controllers\Administracion\PDF\BoletinController as AdminBoletinController;
use App\Http\Controllers\Administracion\PDF\EDescriptivaController as AdminEDescriptivaController;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Baremo;

use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

class BoletinController extends Controller
{
    protected $representant,$estudiants,$list_comment,$admin_boletin,$admin_edescriptiva;

    public function __construct()
    {
        $this->middleware(['auth','is_representant', 'is_time', function ($request, $next) {
            $this->representant = Representant::where('user_id',Auth::user()->id)->first();
            $this->list_comment = Representant::COLUMN_COMMENTS;
            $this->estudiants = ($this->representant) ? $this->representant->estudiants : null;
            $this->admin_boletin = new AdminBoletinController;
            $this->admin_edescriptiva = new AdminEDescriptivaController;
            return $next($request);
        }]);
    }

    public function corte($estudiant_id,$lapso_id)
    {
        $representant = $this->representant;
        $boletin = $this->admin_boletin;
        $estudiant = Estudiant::where('representant_id',$representant->id)->where('id',$estudiant_id)->first();
        $lapso = Lapso::findOrFail($lapso_id);

        $estudiant = Estudiant::findOrFail($estudiant_id);

        // -------------------------------
        // 🔒 VALIDACIÓN DE SOLVENCIA
        // -------------------------------
        $exchange_ammount_expire_bill = round($estudiant->exchange_ammount_expire_bill, 2);

        if ($exchange_ammount_expire_bill > 0) {
            return response()->view('errors.estudiant-no-solvent', [], 403);
        }
        // -------------------------------

        if (!$lapso->getStatusEnableCorte($estudiant_id)) {
            Session::flash('operp_no_ok','Acción no permitida');
            return redirect()->route('home');
        }

        return ($estudiant) ? $boletin->corte($estudiant_id,$lapso_id) : null;
    }

    public function boletin($estudiant_id,$lapso_id){
        $orientacion = 'portrait'; //'portrait', 'landscape'
        $paper  = 'lettet'; //legal, lettet
        $baremo = new Baremo();

        $estudiant = Estudiant::findOrFail($estudiant_id);

        // -------------------------------
        // 🔒 VALIDACIÓN DE SOLVENCIA
        // -------------------------------
        $exchange_ammount_expire_bill = round($estudiant->exchange_ammount_expire_bill, 2);

        if ($exchange_ammount_expire_bill > 0) {
            return response()->view('errors.estudiant-no-solvent', [], 403);
        }
        // -------------------------------

        $lapso = Lapso::findOrFail($lapso_id);

        $seccion = $estudiant->inscripcion->seccion;
        $grado = $seccion->grado;
        $pestudio = $grado->pestudio;
        $baremos = $pestudio->baremos;
        //$baremos = $pestudio->getBaremos($lapso->id ?? null);
        $profesor_guia = $grado->profesor_guias->where('seccion_id',$seccion->id)->first();
        $pensums = $grado->pensums->sortBy(function ($value, $key) {
            return (!empty($value->asignatura->order)) ? $value->asignatura->order:null;
        });
        $pensums_subareas = $grado->pensums_subareas;

        $lapsos = Lapso::all();

        $institucion = Institucion::orderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');

        $view = View::make('administracion.boletins.pdf.'.$pestudio->code,
            compact(
                'estudiant','seccion','grado','pestudio','baremos','baremo',
                'profesor_guia','pensums','lapsos','lapso','lapso_id',
                'institucion','autoridad1','autoridad2'
            )
        )->render();

        $pdf = App::make('dompdf.wrapper')
            ->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]);

        $pdf->loadHTML($view)->setPaper($paper, $orientacion);

        $name_file = 'Informe de Notas';

        if (env('APP_ENV')=="local") return $view;
        else return $pdf->stream($name_file);
    }


    public function sabana($estudiant_id,$lapso_id,$pensum_id)
    {
        $representant = $this->representant;
        $boletin = $this->admin_boletin;
        $estudiant = Estudiant::where('representant_id',$representant->id)->where('id',$estudiant_id)->first();

        $lapso = Lapso::findOrFail($lapso_id);

        if (!$lapso->getStatusEnableCorte($estudiant_id)) {
            Session::flash('operp_no_ok','Acción no permitida');
            return redirect()->route('home');
        }

        // return ($estudiant) ? $boletin->sabana_simple($grado_id,$seccion_id,$lapso_id,$pensum_id) : null;
    }
    public function edescriptiva($estudiant_id)
    {
        $representant = $this->representant;
        $edescriptiva = $this->admin_edescriptiva;
        $estudiant = Estudiant::where('representant_id',$representant->id)->where('id',$estudiant_id)->first();
        return ($estudiant) ? $edescriptiva->edescriptiva($estudiant_id) : null;
    }
}
