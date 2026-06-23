<?php

namespace App\Models\app\HistoricoNota;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hpensum extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'pestudio_id','estudiant_id','observations'
    ];
    /*********************************************************************/
    public function pestudio()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Pestudio');
    }
    public function estudiant()
    {
        return $this->belongsTo('App\Models\app\Estudiant');
    }
    /*********************************************************************/
}
