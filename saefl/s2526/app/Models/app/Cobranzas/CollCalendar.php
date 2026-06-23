<?php

namespace App\Models\app\Cobranzas;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollCalendar extends Model
{
    use HasFactory;

    protected $fillable = ['coll_political_id','name','description','date','time','timestamp','status_active','status_email','status_whatsapp'];

    const COLUMN_COMMENTS = [
        'id' => 'ID del evento del calendario',
        'coll_political_id' => 'ID de la colección política asociada',
        'name' => 'Nombre',
        'description' => 'Descripción',
        'date' => 'Fecha',
        'time' => 'Hora',
        'timestamp' => 'Tiempo unix',
        'status_active' => '¿El evento del calendario está activo?',
'status_email' => 'Envío por correo electrónico ',
        'status_whatsapp'=>'Envio por WhatsApp',
    ];

    public function coll_political()
    {
        return $this->belongsTo(CollPolitical::class,'coll_political_id');
    }

    public function getDateTimeAttribute() {
        return Carbon::createFromFormat('Y-m-d H:i:s',$this->date.' '.$this->time);
    }

    public function getDateTimeTimestampAttribute() {
        return Carbon::parse($this->timestamp);
    }

    public function scopeActive($query, $flag=true)
    {
        return $query->where('coll_calendars.status_active', $flag);
    }
}
