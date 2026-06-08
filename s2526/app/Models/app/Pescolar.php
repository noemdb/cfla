<?php

namespace App\Models\app;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;

use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Pestudio;

class Pescolar extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'institucion_id','name','description','fecha_work','date_begin','finicial','ffinal'
    ];

    /*********************************************************************/
    public function institucion()
    {
        return $this->belongsTo('App\Models\app\Institucion');
    }
    public function peducativos()
    {
        return $this->hasMany('App\Models\app\Pescolar\Peducativo');
    }
    public function historico_notas()
    {
        return $this->hasMany('App\Models\app\HistoricoNota');
    }
    /*********************************************************************/

    public function getStatusBeginAttribute($position)
    {
        $now = Carbon::now()->format('Y-m-d');
        return ($now >= $this->date_begin) ? true : false ;
    }

    public function getAuthorityAttribute($position)
    {
        $autoridads = Pescolar::select('autoridads.*')
            ->join('institucions', 'pescolars.institucion_id', '=', 'institucions.id')
            ->join('autoridads', 'autoridads.institucion_id', '=', 'institucions.id')
            ->Where('pescolars.id', '=', $this->id)
            ->Where('autoridads.position', $position)
            ->Where('autoridads.finicial', '<=', Carbon::now())
            ->Where('autoridads.ffinal', '<=', Carbon::now())
            ->first();

        // dd($autoridads);

        return ($autoridads) ? $autoridads : 0;
    }

    public static function pescolar_list() /* usada para llenar los objetos de formularios select*/
    {
        return Pescolar::select('pescolars.*')->orderby('pescolars.name','asc')->pluck('name', 'id');
    }

    public function scopeActive($query)
    {
        return $query->Where('pescolars.finicial', '<=', Carbon::now())->Where('pescolars.ffinal', '>=', Carbon::now())->orderBy('pescolars.created_at','desc');
    }

}
