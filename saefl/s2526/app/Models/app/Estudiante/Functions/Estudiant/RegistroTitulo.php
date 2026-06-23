<?php
namespace App\Models\app\Estudiante\Functions\Estudiant;

use App\Models\app\HistoricoNota\Hnota;
use App\Models\app\HistoricoNota\Oinstitucion;
use App\Models\app\RegistroTitulo\Titulo;

trait RegistroTitulo
{

    public function getTitulo($registro_titulo_id=null)
    {
        $titulo = Titulo::select('titulos.*')
            ->join('registro_titulos', 'registro_titulos.id', '=', 'titulos.registro_titulo_id')
            ->where('titulos.estudiant_id',$this->id)
            ->WhereNull('registro_titulos.deleted_at')
            ->orderBy('titulos.created_at','desc');

        $titulo = ($registro_titulo_id) ? $titulo->where('registro_titulos.id',$registro_titulo_id) : $titulo;

        $titulo = $titulo->first();

        return $titulo;
    }

    public function getStatusTituloAttribute()
    {
        $titulo = $this->getTitulo();
        return ($titulo) ? true : false;
    }


}
