<?php

namespace App\Http\Controllers\Admin\FixDB;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\GrupoEstable;
use App\Models\app\HistoricoNota;
use App\Models\app\HistoricoNota\Hnota;
use App\Models\app\Institucion;
use App\Models\app\Pescolar\Pensum;

trait UpdateHnotas {

    public static function update_hnotas_grupo_estable_id()
    {
        $file = "UpgradeGrupoEstableId";
        $folder = "hnotas";
        $csvFile = public_path().'/csv/'.$folder.'/'.$file.'.csv';
        $arr_data = csv_to_array($csvFile,";"); //dd($arr_data);
        $datas = collect();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();

        foreach ($arr_data as $k => $row) {
            $estudiant = Estudiant::find($row['estudiant_id']); //dd($row,$estudiant);
            if ($estudiant) {                
                $pensum = Pensum::find($row['pensum_id']); //dd($row,$estudiant,$pensum);
                if ($pensum) {
                    $status_revision = $estudiant->getNotaRevisionStatus($pensum->id);
                    $tevaluacion_id = ($status_revision) ? '3' : '1' ;
                    $historico_nota = HistoricoNota::find($row['historico_nota_id']); //dd($row,$estudiant,$pensum,$historico_nota);
                    if ($historico_nota) {
                        $grupo_estable = GrupoEstable::find($row['grupo_estable_id']); //dd($row,$estudiant,$pensum,$historico_nota,$grupo_estable);
                        if ($grupo_estable) {                            
                            $arr = [
                                'pensum_id'=>$pensum->id,
                                'estudiant_id'=>$estudiant->id,
                                'historico_nota_id'=>$historico_nota->id,
                                'grupo_estable_id'=>$grupo_estable->id,
                                'institucion_id'=>$institucion->id,
                                'valor'=>$row['valor'],
                                'literal'=>$row['literal'],
                                'tevaluacion_id'=>$tevaluacion_id,
                                'tipo'=>$row['tipo'],
                                'fecha'=>$row['fecha'],
                                'user_id' => 1
                            ];
                            $hnota = Hnota::where('historico_nota_id',$historico_nota->id)->where('pensum_id',$pensum->id)->where('estudiant_id',$estudiant->id)->first();
                            if ($hnota) {
                                $hnota->fill($arr);
                                $hnota->save();
                                $datas->push($hnota);                               
                            } else {
                                $new = Hnota::create($arr);
                                $datas->push($new);
                            }
                            
                        }
                        
                    } else {
                        # code...
                    }
                }
            }
        }

        dd($arr_data,$datas);
    }

}
