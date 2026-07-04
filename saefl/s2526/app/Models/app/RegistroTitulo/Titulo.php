<?php

namespace App\Models\app\RegistroTitulo;

use Illuminate\Database\Eloquent\Model;

class Titulo extends Model
{

    protected $fillable = [
        'registro_titulo_id','estudiant_id','seccion_id','serie','observations'
    ];
    const COLUMN_COMMENTS = ['registro_titulo_id' => 'Promoción','estudiant_id'=>'Estudiante','seccion_id'=>'Sección','serie'=>'Serial del título','observations'=>'Observaciones'];
    /*********************************************************************/
    public function registro_titulo()
    {
        return $this->belongsTo('App\Models\app\HistoricoNota');
    }
    public function estudiant()
    {
        return $this->belongsTo('App\Models\app\Estudiant');
    }
    public function seccion()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Seccion');
    }

}
