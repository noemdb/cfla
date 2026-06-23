<?php

namespace App\Http\Controllers\Inicial\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Inicial\Eifinalk;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Profesor;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EifinalkController extends Controller
{
    public $user, $autoridad, $list_comment_autoridad;

    public function __construct()
    {
        $this->middleware(['auth', 'is_inicial', function ($request, $next) {
            $this->user = User::find(Auth::id());
            $this->autoridad = Autoridad::where('user_id', Auth::id())->first(); //dd(Auth::id(),$this->autoridad);
            $this->list_comment_autoridad = Autoridad::COLUMN_COMMENTS;
            return $next($request);
        }]);
    }

    public function index()
    {
        $user = $this->user;
        $autoridad = $this->autoridad;
        $list_comment_autoridad = $this->list_comment_autoridad;

        $lapsos = Lapso::all();
        $lapso_active = Lapso::current();

        return view('inicials.eifinalks.index', compact('user', 'autoridad', 'list_comment_autoridad', 'lapsos', 'lapso_active'));
    }

    /**
     * Muestra el formato imprimible del informe final
     *
     * @param Eifinalk $eifinalk
     * @return \Illuminate\View\View
     */
    public function print(Eifinalk $eifinalk)
    {
        // Cargar las relaciones necesarias
        $eifinalk->load([
            'pevaluacion.pensum.grado',
            'pevaluacion.seccion',
            'pevaluacion.lapso',
            'pevaluacion.profesor',
            'estudiant',
            'expectations.area'
        ]);

        $profesor = Profesor::where('user_id', Auth::user()->id)->first();
        $institucion = Institucion::OrderBy('created_at', 'DESC')->first();
        $fecha = Carbon::now()->format('d-m-Y h:m A');

        return view('livewire.inicial.formats.eifinalks.index', compact('eifinalk', 'profesor', 'institucion', 'fecha'));
    }

    /**
     * Muestra el formato imprimible de todos los informes finales de un estudiante
     *
     * @param Estudiant $estudiant
     * @param Lapso $lapso
     * @return \Illuminate\View\View
     */
    public function printAllforLapso(Estudiant $estudiant, Lapso $lapso)
    {
        // Cargar todos los informes finales del estudiante con sus relaciones
        $eifinalks = $estudiant->eifinalks()
            ->with([
                'pevaluacion.pensum.grado',
                'pevaluacion.seccion',
                'pevaluacion.lapso',
                'pevaluacion.profesor',
                'expectations.area'
            ])
            ->whereHas('pevaluacion', function ($query) use ($lapso) {
                $query->where('lapso_id', $lapso->id);
            })
            // ->orderBy('created_at', 'desc')
            ->orderBy('order', 'asc')
            ->get();

        $eifinalks_oficial = $estudiant->eifinalks()
            ->with([
                'pevaluacion.pensum.grado',
                'pevaluacion.seccion',
                'pevaluacion.lapso',
                'pevaluacion.profesor',
                'expectations.area'
            ])
            ->whereHas('pevaluacion', function ($query) use ($lapso) {
                $query->where('lapso_id', $lapso->id)
                    ->where('status_official', true);
            })
            ->orderBy('order', 'asc')
            ->get();

        $eifinalks_component = $estudiant->eifinalks()
            ->with([
                'pevaluacion.pensum.grado',
                'pevaluacion.seccion',
                'pevaluacion.lapso',
                'pevaluacion.profesor',
                'expectations.area'
            ])
            ->whereHas('pevaluacion', function ($query) use ($lapso) {
                $query->where('lapso_id', $lapso->id)
                    ->where('status_official', false);
            })
            ->orderBy('order', 'asc')
            ->get(); //dd($eifinalks_oficial,$eifinalks_component);

        $profesor = Profesor::where('user_id', Auth::user()->id)->first();
        $institucion = Institucion::OrderBy('created_at', 'DESC')->first();
        $fecha = Carbon::now()->format('d-m-Y h:m A');

        return view('livewire.inicial.formats.eifinalks.index-all', compact('estudiant', 'lapso', 'eifinalks','eifinalks_oficial','eifinalks_component', 'profesor', 'institucion', 'fecha'));
    }
}
