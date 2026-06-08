<?php
namespace App\Http\Controllers\Administracion\Tab\Functions\Boletins;

use Illuminate\Http\Request;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Boletin;
use App\Models\app\Estudiante\BoletinAjuste;
use App\Models\app\Pescolar\Baremo;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Pevaluacion;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait Crud {


    public function positions(Request $request)
    {
        /*******************request****************************/
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id:null;

        $grado = Grado::find($grado_id);
        $seccion = Seccion::find($seccion_id);
        $lapso = Lapso::find($lapso_id);

        /*******************inicializaciones****************************/
        $lapsos = Lapso::all();
        $estudiants = collect();

        /*******************query****************************/
        if (count($request->all())>0) {

            $estudiants = $seccion->getEstudiantPosicionPromedioLapso($lapso_id); //dd($estudiants);
        }

        /*******************list****************************/
        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : collect();
        $list_lapso = Lapso::select('code', 'id')->orderby('name','asc')->pluck('code', 'id'); //$list_lapso->put(null,'Final');

        $compact = ['estudiants','lapsos',
        //'notasSeccion',
        'grado','seccion','lapso',
        'grado_id','seccion_id','lapso_id',
        'list_grado','list_seccion','list_lapso'];

        return view('administracion.boletins.positions',compact($compact));
    }

    public function indicators(Request $request)
    {
        $lapsos = Lapso::all();
        $lapso_active = Lapso::current();

        $pestudios = Pestudio::active()->get();

        $compact = [
            'lapsos','lapso_active',
            'pestudios',
            ];

        return view('administracion.boletins.indicators',compact($compact));
    }

    public function corte(Request $request)
    {
        /*******************request****************************/
        $search = (!empty($request->search)) ? $request->search : null ;
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id:null;

        /*******************inicializaciones****************************/
        $estudiants = collect();
        $lapsos = Lapso::all();
        $lapso = Lapso::find($lapso_id);

        /*******************query****************************/
        if (count($request->all())>0) {

            $estudiants = Estudiant::WidthInscripcion()
            ->select('estudiants.*')
            //->join('seccions','seccions.id','=','inscripcions.seccion_id')
            ->join('grados','grados.id','=','seccions.grado_id')
            ->orderBy('created_at','desc');

        $estudiants = ($grado_id) ? $estudiants->where('grados.id',$grado_id) : $estudiants ;
        $estudiants = ($seccion_id) ? $estudiants->where('seccions.id',$seccion_id) : $estudiants ;

        $arr_get = [ 'search'=>$search];
        $estudiants = ($search) ? $estudiants->name($arr_get) : $estudiants ;

        /*******************get collections****************************/
            $estudiants = $estudiants->get(); //  dd($estudiants);
        }

        /*******************list****************************/
        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : collect();
        $list_lapso = Lapso::select('code', 'id')->orderby('name','asc')->pluck('code', 'id'); //$list_lapso->put(null,'Final');

        $compact = ['search','estudiants','lapsos','lapso','grado_id','seccion_id','list_grado','list_seccion','lapso_id','list_lapso'];

        return view('administracion.boletins.corte',compact($compact));
    }

    public function crud(Request $request)
    {
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id: null;
        $pensums_id = (!empty($request->pensums_id)) ? $request->pensums_id: null;
        $profesor_id = (!empty($request->profesor_id)) ? $request->profesor_id: null;
        $estudiant_id = (!empty($request->estudiant_id)) ? $request->estudiant_id: null;
        $help_estudiant = (!empty($request->help_estudiant)) ? $request->help_estudiant: null;
        $enable_academic_index = (!empty($request->enable_academic_index)) ? $request->enable_academic_index: null;

        $finicial = (!empty($request->finicial)) ? $request->finicial: null;
        $ffinal = (!empty($request->ffinal)) ? $request->ffinal: null;

        $boletins = collect();

        if (count($request->all())>0) {

            $boletins = Boletin::select('boletins.*')
                ->join('evaluacions','evaluacions.id','=','boletins.evaluacion_id')
                ->join('pevaluacions','pevaluacions.id','=','evaluacions.pevaluacion_id')
                ->join('pensums','pensums.id','=','pevaluacions.pensum_id')
                ->join('asignaturas','asignaturas.id','=','pensums.asignatura_id')
                ->join('grados', 'grados.id', '=', 'pensums.grado_id')
                ->join('seccions', 'grados.id', '=', 'seccions.grado_id')
                ->wherenull('pevaluacions.deleted_at')
                ->orderby('boletins.created_at')
                ->groupby('boletins.id');

                $boletins = ($estudiant_id) ? $boletins->where('boletins.estudiant_id',$estudiant_id) : $boletins ;
                $boletins = ($grado_id) ? $boletins->where('pensums.grado_id',$grado_id) : $boletins ;
                $boletins = ($seccion_id) ? $boletins->where('pevaluacions.seccion_id',$seccion_id) : $boletins ;
                $boletins = ($lapso_id) ? $boletins->where('pevaluacions.lapso_id',$lapso_id) : $boletins ;
                $boletins = ($pensums_id) ? $boletins->where('pevaluacions.pensum_id',$pensums_id) : $boletins ;
                $boletins = ($profesor_id) ? $boletins->where('pevaluacions.profesor_id',$profesor_id) : $boletins ;

                $boletins = ($finicial) ? $boletins->whereDate('boletins.created_at','>=',$finicial) : $boletins ;
                $boletins = ($ffinal) ? $boletins->whereDate('boletins.created_at','<=',$ffinal) : $boletins ;

                $boletins = ($enable_academic_index=='SI') ? $boletins->where('asignaturas.enable_academic_index','true') : $boletins ;
                $boletins = ($enable_academic_index=='NO') ? $boletins->where('asignaturas.enable_academic_index','false') : $boletins ;

                $boletins = $boletins->get();
        }

        /* list para los select-html */
        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : collect();
        $list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $list_pensum    = Pensum::list_pestudio_pensum($grado_id);
        $list_profesor = Profesor::list_profesors();
        $list_estudiants = Estudiant::list_pestudio_grado();

        return view('administracion.boletins.crud',
        compact('boletins','grado_id','list_grado','enable_academic_index','seccion_id','estudiant_id','list_seccion','finicial','ffinal','lapso_id','list_lapso','list_pensum','pensums_id','list_profesor','profesor_id','list_estudiants','help_estudiant'));
    }

    public function sabanafull(Request $request)
    {
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;

        $pestudios = Pestudio::active('true')->get();
        $estudiants = collect();
        $grado = collect();
        $seccion = collect();
        $lapso = collect();
        $pensums = collect();
        $escala = collect();
        $lapsos = Lapso::all();
        $baremo = new Baremo();

        if ($grado_id && $seccion_id) {

            $grado = Grado::find($grado_id);

            $seccion = Seccion::find($seccion_id);

            $pensums = Pensum::where('grado_id',$grado_id)->get();

            $escala = $grado->pestudio->escala;

            $estudiants = Estudiant::select('estudiants.*')
                ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->join('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
                ->join('grados', 'seccions.grado_id', '=', 'grados.id')
                ->wherenull('inscripcions.deleted_at')
                ->wherenull('seccions.deleted_at')
                ->wherenull('grados.deleted_at')
                ->Where('estudiants.status_active','true')
                ->where('grados.status_active','true')
                ->where('seccions.status_active','true')
                ->orderby('estudiants.ci_estudiant');

            $estudiants = ($grado_id) ? $estudiants->where('grados.id',$grado_id) : $estudiants ;
            $estudiants = ($seccion_id) ? $estudiants->where('seccions.id',$seccion_id) : $estudiants ;

            $estudiants = $estudiants->get();
        }

        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : collect();

        return view('administracion.boletins.sabanafull',compact(
            'pestudios','estudiants','grado_id','grado','seccion_id','seccion','pensums','lapsos',
            'list_grado','list_seccion','escala','baremo'
        ));
    }

    public function crud_ajuste(Request $request)
    {
        $ci_estudiant = (!empty($request->ci_estudiant)) ? $request->ci_estudiant : null ;
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ; //dd($grado_id);
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id:null;

        $boletin_ajustes = collect();

        /*******************query****************************/
        if (count($request->all())>0) {

            $boletin_ajustes = BoletinAjuste::select('boletin_ajustes.*')
            ->join('estudiants','estudiants.id','=','boletin_ajustes.estudiant_id')
            ->join('pevaluacions','pevaluacions.id','=','boletin_ajustes.pevaluacion_id')
            ->join('lapsos','lapsos.id','=','pevaluacions.lapso_id')
            ->join('seccions','seccions.id','=','pevaluacions.seccion_id')
            ->join('grados','grados.id','=','seccions.grado_id')
            ->wherenull('pevaluacions.deleted_at')
            // ->join('seccions','grados.id','=','seccions.grado_id')
            ->orderBy('created_at','desc')
            ->groupBy('boletin_ajustes.id');

            $boletin_ajustes = ($ci_estudiant) ? $boletin_ajustes->where('estudiants.ci_estudiant',$ci_estudiant) : $boletin_ajustes ;
            $boletin_ajustes = ($grado_id) ? $boletin_ajustes->where('grados.id',$grado_id) : $boletin_ajustes ;
            $boletin_ajustes = ($seccion_id) ? $boletin_ajustes->where('seccions.id',$seccion_id) : $boletin_ajustes ;
            $boletin_ajustes = ($lapso_id) ? $boletin_ajustes->where('lapsos.id',$lapso_id) : $boletin_ajustes ;

            /*******************get collections****************************/

            $boletin_ajustes = $boletin_ajustes->get(); //dd($boletin_ajustes);
        }

        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : collect();
        $list_lapso = Lapso::select('code', 'id')->orderby('name','asc')->pluck('code', 'id'); //$list_lapso->put(null,'Final');


        // $boletin_ajustes = BoletinAjuste::all();
        return view('administracion.boletins.crud_ajuste',compact(
            'boletin_ajustes','ci_estudiant','grado_id','seccion_id','lapso_id','list_grado','list_seccion','list_lapso'));
    }

    public function boletin(Request $request)
    {
        /*******************request****************************/
        $search = (!empty($request->search)) ? $request->search : null ;
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id:null;

        /*******************inicializaciones****************************/
        $estudiants = collect();
        $pensums = collect();
        $lapsos = Lapso::all();

        /*******************query****************************/
        if (count($request->all())>0) {

            $estudiants = Estudiant::WidthInscripcion()
            ->select('estudiants.*')
            // ->join('seccions','seccions.id','=','inscripcions.seccion_id')
            ->join('grados','grados.id','=','seccions.grado_id')

            // ->where('grados.id',$grado_id)
            // ->where('seccions.id',$seccion_id)

            ->orderBy('created_at','desc');

        $estudiants = ($grado_id) ? $estudiants->where('grados.id',$grado_id) : $estudiants ;
        $estudiants = ($seccion_id) ? $estudiants->where('seccions.id',$seccion_id) : $estudiants ;

        $arr_get = [ 'search'=>$search];
        $estudiants = ($search) ? $estudiants->name($arr_get) : $estudiants ;

        $pensums = Pensum::Where('grado_id',$grado_id)->get();

        /*******************get collections****************************/

            $estudiants = $estudiants->get();

        //  dd($estudiants);
        }


        /*******************list****************************/
        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : collect();
        $list_lapso = Lapso::select('code', 'id')->orderby('name','asc')->pluck('code', 'id'); //$list_lapso->put(null,'Final');

        return view('administracion.boletins.boletin',compact(
            'search','estudiants','pensums','lapsos','grado_id','seccion_id','lapso_id','list_grado','list_seccion','list_lapso'
        ));
    }

    public function ajustes(Request $request)
    {
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id:null;
        $grado = Grado::where('id',$grado_id)->first();

        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id:null;
        $seccion = Seccion::where('id',$seccion_id)->first();

        $pensum_id = (!empty($request->pensum_id)) ? $request->pensum_id:null;
        $pensum = Pensum::where('id',$pensum_id)->first();

        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id:null;
        $lapso = Lapso::where('id',$lapso_id)->first();

        $estudiants = collect();
        $pevaluacion = collect();

        if ($grado_id && $seccion_id && $pensum_id && $lapso_id) {

            $estudiants = $seccion->estudiants_in;

            $pevaluacion = Pevaluacion::where('pensum_id',$pensum_id)
                ->where('seccion_id',$seccion_id)
                ->where('lapso_id',$lapso_id)
                ->first();
        }

        // dd($grado_id,$seccion_id,$lapso_id,$pevaluacion);

        // $pestudios = Pestudio::active('true')->with('grados')->orderBy('id', 'asc')->get();
        // foreach ($pestudios as $pestudio) {
        //     $list_grado[$pestudio->name] = $pestudio->grados->pluck('name', 'id');
        // }
        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = (!empty($grado)) ? $grado->seccions()->pluck('name', 'id'): array() ;
        $list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        $list_pensum  = array() ;
        if (!empty($grado)) {
            $list_pensum  = Pensum::select('pensums.id as id','asignaturas.name as name')
            ->join('asignaturas','asignaturas.id','=','pensums.asignatura_id')
            ->where('grado_id','=',$grado->id)->pluck('name', 'id');
        }

        return view('administracion.boletins.ajustes',compact(
            'estudiants','pevaluacion','grado_id','grado','seccion_id','seccion','lapso_id','lapso','pensum_id','pensum',
            'list_grado','list_seccion','list_lapso','list_pensum'
        ));
    }

    public function sabana(Request $request)
    {
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id: null;

        $pestudios = Pestudio::active('true')->get();
        $estudiants = collect();
        $grado = collect();
        $seccion = collect();
        $lapso = collect();
        $pensums = collect();
        $escala = collect();

        if ($grado_id && $seccion_id && $lapso_id) {

            $grado = Grado::find($grado_id);

            $seccion = Seccion::find($seccion_id);

            $lapso = Lapso::find($lapso_id);

            $pensums = Pensum::where('grado_id',$grado_id)->get();


            $escala = $grado->pestudio->escala;

            $estudiants = Estudiant::select('estudiants.*')
                ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->join('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
                ->join('grados', 'seccions.grado_id', '=', 'grados.id')
                ->wherenull('inscripcions.deleted_at')
                ->wherenull('seccions.deleted_at')
                ->wherenull('grados.deleted_at')
                ->Where('estudiants.status_active','true')
                ->where('grados.status_active','true')
                ->where('seccions.status_active','true')
                ->orderby('estudiants.ci_estudiant');

            $estudiants = ($grado_id) ? $estudiants->where('grados.id',$grado_id) : $estudiants ;
            $estudiants = ($seccion_id) ? $estudiants->where('seccions.id',$seccion_id) : $estudiants ;

            $estudiants = $estudiants->get();
        }

        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : collect();
        $list_lapso = Lapso::list_lapso_final();

        return view('administracion.boletins.sabana',compact(
            'pestudios','estudiants','grado_id','grado','seccion_id','seccion','lapso_id','lapso','pensums',
            'list_grado','list_seccion','list_lapso','escala'
        ));
    }


}
