<?php
namespace App\Models\app\Estudiante\Functions\Estudiant;

use App\Models\app\Estudiante\BoletinAjuste;
use App\Models\app\Estudiante\Functions\Estudiant\BoletinRevisions;
use App\Models\app\Estudiante\Functions\Estudiant\Evaluacions;
use App\Models\app\Estudiante\Functions\Estudiant\Notas;
use App\Models\app\Estudiante\Functions\Estudiant\Pevaluacions;
use App\Models\app\Estudiante\Functions\Estudiant\Promedios;
use App\Models\app\Pescolar\Baremo;
use App\Models\app\Pescolar\Lapso;
use Illuminate\Support\Facades\DB;

trait Boletins
{
    use Notas;
    use Promedios;
    use Pevaluacions;
    use Evaluacions;
    use BoletinRevisions;

    public function getAllAjuste($pensum_id = null, $pevaluacion_id = null)
    {
        $boletin_ajuste = BoletinAjuste::selectRaw('sum(boletin_ajustes.ajuste) as ajuste')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'boletin_ajustes.pevaluacion_id')
            ->where('boletin_ajustes.estudiant_id', $this->id)
            ->wherenull('pevaluacions.deleted_at');

        $boletin_ajuste = ($pensum_id) ? $boletin_ajuste->where('pevaluacions.pensum_id', $pensum_id) : $boletin_ajuste;
        $boletin_ajuste = ($pevaluacion_id) ? $boletin_ajuste->where('pevaluacions.id', $pevaluacion_id) : $boletin_ajuste;

        $boletin_ajuste = $boletin_ajuste->first();

        return ($boletin_ajuste) ? $boletin_ajuste->ajuste : null;
    }

    public function getAjuste($pevaluacion_id = null)
    {
        $boletin_ajuste = BoletinAjuste::where('estudiant_id', $this->id)->where('pevaluacion_id', $pevaluacion_id)->first();

        return ($boletin_ajuste) ? $boletin_ajuste->ajuste : null;
    }

    public function getAjusteLapso($lapso_id)
    {
        $boletin_ajuste = DB::table('estudiants')
            ->select(
                DB::raw('sum(boletin_ajustes.ajuste) as sum_ajuste')
            )
            ->join('boletin_ajustes', 'estudiants.id', '=', 'boletin_ajustes.estudiant_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'boletin_ajustes.pevaluacion_id')
            ->Where('estudiants.id', $this->id)
            ->Where('pevaluacions.lapso_id', $lapso_id)
            ->groupby('estudiants.id')
            ->first();

        return (! empty($boletin_ajuste)) ? $boletin_ajuste->sum_ajuste : null;
    }

    public function getLiteralAttribute()
    {
        $promedio = $this->getPromedioFinal(2);
        $pestudio = $this->pestudio;

        $lapso    = Lapso::first();
        $lapso_id = $lapso ? $lapso->id : null;

        if (! $pestudio) {
            return null;
        }

        if ($promedio !== null) {
            $literal = Baremo::getLiteral($pestudio->id, $promedio, $lapso_id);
            return $literal ? $literal : $promedio;
        }

        $baremo = Baremo::whereNull('minimo')
            ->whereNull('maxima')
            ->where('pestudio_id', $pestudio->id);

        if ($lapso_id) {
            $baremo->where(function ($q) use ($lapso_id) {
                $q->where('lapso_id', $lapso_id)
                    ->orWhereNull('lapso_id');
            })->orderBy('lapso_id', 'desc');
        } else {
            $baremo->whereNull('lapso_id');
        }

        $baremo = $baremo->first();

        return ($baremo) ? $baremo->literal : $promedio;
    }
}
