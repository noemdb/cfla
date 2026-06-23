<?php

namespace App\Http\Controllers\Admin\Fix\DB\Functions\Control;

use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\HistoricoNota;
use App\Models\app\HistoricoNota\Hnota;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\RegistroPagoCombinado;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait FixEvaluacion {

    public function fix_delete_evaluacion()
    {
        $datas = collect([]);
        $fecha = Carbon::now()->format('Y-m-d');

        $pevaliacions = Pevaluacion::where('lapso_id','>',1)->get();

        foreach ($pevaliacions as $pevaliacion) {

            $evaluacions = $pevaliacion->evaluacions;
            foreach ($evaluacions as $evaluacion) {
                $datas->push($evaluacion);
                $delete = $evaluacion->delete();
            }
        }

        // dd($datas->toarray());

    }

    public function fix_fill_pevaluacion()
    {
        $datas = collect([]);
        $carbon = new Carbon;

        $pevaliacions =
            Pevaluacion::select('pevaluacions.*')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('pestudios', 'pestudios.id', '=', 'pensums.pestudio_id')

            ->where('pestudios.id',2)
            ->where('pevaluacions.lapso_id','>',1)

            ->wherenull('pestudios.deleted_at')
            ->wherenull('pensums.deleted_at')
            ->get();

        foreach ($pevaliacions as $pevaliacion) {

            $finicial = $pevaliacion->lapso->finicial;
            $ffinal = $pevaliacion->lapso->ffinal;

            $fecha = Factory::create()->dateTimeBetween($finicial, $ffinal)->format('Y-m-d');

            $arr = [
                'pevaluacion_id'=>$pevaliacion->id,
                'escala_id'=>$pevaliacion->pensum->pestudio->escala->id,
                'fecha'=>$fecha,
                'description'=>'Nota final de la Asignatura',
                'observations'=>'Nota única'
            ];
            $create = Evaluacion::create($arr);
            $datas->push($arr);
        }

        // dd($datas->toarray());

    }
    public function fix_hnotas_add_hn_id(Request $request)
    {
        $historico_notas = HistoricoNota::OrderBy('id')->GroupBy('estudiant_id')->get();
        // dd($historico_notas);
        $datas = collect([]);
        foreach ($historico_notas as $historico_nota) {

            $hnotas = Hnota::select('hnotas.*')
                ->join('pensums', 'pensums.id', '=', 'hnotas.pensum_id')
                ->where('hnotas.estudiant_id',$historico_nota->estudiant_id)
                ->GroupBy('pensums.id')
                ->get();

            foreach ($hnotas as $hnota) {
                $hnota->fill(['historico_nota_id'=>$historico_nota->id]);
                $hnota->save();
                DB::commit();
            }

            $datas->push($historico_nota);
        }

        dd($datas);
    }

    public function fix_inscripcions_not_estudiant(Request $request)
    {
        $inscripcions = Inscripcion::all();
        $datas = collect([]);
        foreach ($inscripcions as $inscripcion) {
            if (empty($inscripcion->estudiant->id)) {
                // $inscripcion = Inscripcion::findOrFail($inscripcion->id);
                $inscripcion->delete();
                $datas->push($inscripcion);
            }
        }
        dd($datas);
    }

    public function fix_state_active_profesors(Request $request)
    {
        $pevaluacions = Pevaluacion::withTrashed()->get();

        $datas = collect([]);

        foreach ($pevaluacions as $pevaluacion) {

            $profesor_id = $pevaluacion->profesor_id;

            $profesor = Profesor::withTrashed()->where('id',$profesor_id)->first();

            if (!empty($profesor->deleted_at)) {
                $arr = ['status_active'=>'false','deleted_at'=>null];
                $profesor->fill($arr);
                $profesor->save();
                // $update = Profesor::where('id',$profesor->id)->update(['status_active'=>'false','deleted_at'=>null]);
                // DB::commit($update);
                $datas->push($profesor);
            }

        }

        dd($datas);
    }
}
