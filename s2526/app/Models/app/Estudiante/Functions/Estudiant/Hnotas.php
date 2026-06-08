<?php
namespace App\Models\app\Estudiante\Functions\Estudiant;

use Illuminate\Support\Facades\DB;

use App\Models\app\HistoricoNota\Hnota;
use App\Models\app\HistoricoNota\Oinstitucion;

trait Hnotas
{

    public function GetIA($pestudio_id)
    {
        $notas = Hnota::select('hnotas.*','historico_notas.deleted_at as historico_notas_deleted_at','historico_notas.pestudio_id as historico_notas_pestudio_id')
            ->join('estudiants', 'estudiants.id', '=', 'hnotas.estudiant_id')
            ->join('historico_notas', 'historico_notas.id', '=', 'hnotas.historico_nota_id')
            // ->join('historico_notas', 'estudiants.id', '=', 'historico_notas.estudiant_id')
            ->join('pensums', 'pensums.id', '=', 'hnotas.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->where('historico_notas.pestudio_id',$pestudio_id)
            ->where('hnotas.estudiant_id',$this->id)
            ->where('asignaturas.enable_academic_index','true' )
            ->WhereNotNull('hnotas.valor')
            ->WhereNull('historico_notas.deleted_at')
            ->WhereNull('hnotas.deleted_at')
            ->WhereNull('estudiants.deleted_at')
            ->WhereNull('pensums.deleted_at')
            ->WhereNull('asignaturas.deleted_at')
            ->get(); //dd($notas);
        $ia = ($notas->count() > 0) ? round(($notas->sum('valor')/$notas->count()),2) :null;
        return $ia;
    }

    public function GetAllInstitucions($pestudio_id)
    {
        $oinstitucions = Oinstitucion::select('oinstitucions.*', 'grados.order as grados_order')
            ->join('hnotas', 'oinstitucions.id', '=', 'hnotas.institucion_id')
            ->join('pensums', 'pensums.id', '=', 'hnotas.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('estudiants', 'estudiants.id', '=', 'hnotas.estudiant_id')
            ->join('historico_notas', 'estudiants.id', '=', 'historico_notas.estudiant_id')
            ->where('historico_notas.pestudio_id', $pestudio_id)
            ->where('hnotas.estudiant_id', $this->id)
            
            //->where('grados.status_active', 'true')
            ->WhereNull('estudiants.deleted_at')
            ->WhereNull('historico_notas.deleted_at')
            //->WhereNull('grados.deleted_at')
            
            //->OrderBy('pensums.grado_id')
            ->OrderBy('grados.order','ASC')

            ->groupby('oinstitucions.id')
            ->get();
        return $oinstitucions;
    }

    public function GetAllHNotas($pestudio_id, $enable_academic_index = 'true', $status_component = 'false')
    {
        $historico_nota = DB::table('historico_notas')->select('historico_notas.*')->where('estudiant_id',$this->id)->orderBy('created_at','desc')->first();
        $notas = Hnota::select('hnotas.*','grados.id as grado_id','grados.code_sm as code_sm')
            ->join('estudiants', 'estudiants.id', '=', 'hnotas.estudiant_id')
            ->join('historico_notas', 'estudiants.id', '=', 'historico_notas.estudiant_id')
            ->join('pensums', 'pensums.id', '=', 'hnotas.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->where('historico_notas.pestudio_id',$pestudio_id)
            ->where('historico_notas.id',$historico_nota->id)
            ->where('hnotas.estudiant_id',$this->id)
            ->where('asignaturas.enable_academic_index', $enable_academic_index )
            ->where('pensums.status_component', $status_component )
            ->WhereNull('estudiants.deleted_at')
            ->WhereNull('pensums.deleted_at')
            ->WhereNull('asignaturas.deleted_at')
            ->orderBy('grados.id','asc')
            //->groupBy('grados.id')
            ->groupBy('pensums.id')
            ->get();

            //if ($enable_academic_index == 'false') dd($notas);

        return $notas;
    }

    public function GetHNotas($grado_id,$enable_academic_index = 'true')
    {
        $notas = Hnota::select('hnotas.*')
            ->join('estudiants', 'estudiants.id', '=', 'hnotas.estudiant_id')
            ->join('pensums', 'pensums.id', '=', 'hnotas.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->where('hnotas.estudiant_id',$this->id)
            ->where('pensums.grado_id',$grado_id)
            ->where('asignaturas.enable_academic_index',$enable_academic_index )
            ->WhereNull('estudiants.deleted_at')
            ->WhereNull('pensums.deleted_at')
            ->WhereNull('asignaturas.deleted_at')
            ->OrderBy('asignaturas.order')
            ->get();
        return $notas;
    }

}
