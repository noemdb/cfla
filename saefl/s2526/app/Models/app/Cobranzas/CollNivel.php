<?php

namespace App\Models\app\Cobranzas;

use Illuminate\Database\Eloquent\Model;

class CollNivel extends Model
{
    protected $fillable = [
        'coll_political_id','name','order','weighing','description','status'
    ];
    protected $dates = ['created_at','updated_at'];

    const COLUMN_COMMENTS = [
        'coll_political_id' => 'Política de Cobranza',
        'name' => 'Nombre',
        'order' => 'Orden',
        'weighing' => 'Ponderación',
        'description' => 'Descripción',
        'status' => 'Estado',
    ];

    public function coll_political()
    {
        return $this->belongsTo('App\Models\app\Cobranzas\CollPolitical');
    }
    public function coll_mensseges()
    {
        return $this->hasMany('App\Models\app\Cobranzas\CollMessege');
    }

    public function getFullNameAttribute()
    {
        $coll_political = $this->coll_political;
        return "{$coll_political->name} - {$this->name} - {$this->description}";
    }

    public static function list_political_nivels() /* usada para llenar los objetos de formularios select*/
    {
        $coll_politicals = CollPolitical::all();
        $datas = collect();
        foreach ($coll_politicals as $coll_political) {
            $datas->put($coll_political->name, $coll_political->coll_nivels->pluck('name', 'id'));
        }
        return $datas;
    }
    public function getListCommentAttribute()
    {
        return CollNivel::COLUMN_COMMENTS;
    }
}
