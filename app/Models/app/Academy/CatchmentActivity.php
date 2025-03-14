<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;

class CatchmentActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'name',
        'description',
        'date',
        'time',
        'status_active'
    ];

    const COLUMN_COMMENTS = [
        'id' => 'ID de la actividad de captación',
        'group_id' => 'Grupo',
        'name' => 'Nombre',
        'description' => 'Descripción',
        'date' => 'Fecha de la actividad',
        'time' => 'Hora de la actividad',
        'status_active' => '¿La actividad de captación está activa?',
    ];
    
    public function catchment_group() { return $this->belongsTo(CatchmentGroup::class, 'group_id'); }

    public function getDateTimeAttribute()
    {
        $date = $this->date;
        $time = $this->time; //dd($date,$time);
        return Date::createFromFormat('Y-m-d H:i:s',$date.' '.$time);

    }
}
