<?php

namespace App\Models\app\Planpago;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [ 'user_id','date','name','description','observations','icon','status_holidays' ];

    // ↓ MANTENER SIN CAST PARA date (como se requiere)
    protected $casts = [
        //'status_holidays' => 'boolean',
        'user_id' => 'integer'
    ];

    const COLUMN_COMMENTS = [
        'user_id'=>'Usuario',
        'date'=>'Fecha del evento',
        'name'=>'Nombre',
        'description'=>'Descripción',
        'observations'=>'Observaciones',
        'icon'=>'Icono',
        'status_holidays'=>'Día feriado/Bancario'
    ];

    const ICONS_LIST = [
        'fas fa-calendar-day' => 'Evento Regular',
        'fas fa-umbrella-beach' => 'Día Feriado',
        'fas fa-gift' => 'Celebración/Navidad',
        'fas fa-flag' => 'Feriado Nacional',
        'fas fa-building' => 'Lunes Bancario',
        'fas fa-graduation-cap' => 'Evento Académico',
        'fas fa-bullhorn' => 'Anuncio Especial',
        'fas fa-clock' => 'Fecha Límite',
        'fas fa-users' => 'Reunión/Asamblea',
        'fas fa-calendar-check' => 'Evento Confirmado'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function getStatusDeleteAttribute()
    {
        $today = Carbon::now()->format('Y-m-d');
        return ($this->date <= $today) ? true : false;
    }

    // ↓ ACCESSOR PARA FECHA FORMATEADA (CORRECTO)
    public function getFormattedDateAttribute()
    {
        return $this->date ? Carbon::parse($this->date)->format('d-m-Y') : '';
    }

    // ↓ ACCESSOR ADICIONAL PARA INPUT DATE (FORMATO Y-m-d)
    public function getDateInputAttribute()
    {
        return $this->date ? Carbon::parse($this->date)->format('Y-m-d') : '';
    }

    public function scopeDeletable($query)
    {
        return $query->where('date', '>', Carbon::now()->format('Y-m-d'));
    }

    public function canBeDeleted()
    {
        return !$this->status_delete;
    }

    // ↓ NUEVO ACCESSOR PARA ICONO CON VALOR POR DEFECTO
    public function getIconAttribute($value)
    {
        return $value ?: 'fas fa-calendar-day'; // Icono por defecto
    }

    // ↓ NUEVO ACCESSOR PARA TEXTO DEL ICONO
    public function getIconTextAttribute()
    {
        return self::ICONS_LIST[$this->icon] ?? 'Evento Regular';
    }
}