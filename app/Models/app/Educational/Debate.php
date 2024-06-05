<?php

namespace App\Models\app\Educational;

use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Seccion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debate extends Model
{
    use HasFactory;
    protected $fillable = [
        'competition_id',
        'grado_id',
        'seccion_id',
        'name',
        'description',
        'status_active',
        'winner_section_id',
        'attachment',
    ];

    const COLUMN_COMMENTS = [
        'competition_id' => 'Competición',
        'grado_id' => 'Grado',
        'grado_full' => 'Grado/Año Sección',
        'seccion_id' => 'Sección',
        'name' => 'Nombre',
        'description' => 'Descripción',
        'status_active' => 'Estado (Activo/Desactivo)',
        'winner_section_id' => 'Sección ganadora',
        'attachment' => 'Archivo adjunto'
    ];

    // Relación
    public function questions() { return $this->hasMany(DebateQuestion::class); }
    public function answers() { return $this->hasMany(DebateAnswer::class); }
    public function grado() { return $this->belongsTo(Grado::class,'grado_id'); }
    public function seccion() { return $this->belongsTo(Seccion::class,'seccion_id'); }
    public function competition() { return $this->belongsTo(DebateCompetition::class,'competition_id'); }
    public function winnerSection() { return $this->belongsTo(Seccion::class, 'winner_section_id'); }

    // Scope para obtener los debates activos
    public function scopeActive($query)
    {
        return $query->where('status_active', true);
    }
    // Scope para obtener los debates finalizados
    public function scopeFinished($query)
    {
        return $query->where('status_active', false);
    }

    // Accessor para obtener el nombre completo del debate
    public function getFullNameAttribute()
    {
        $grado = $this->grado->name;
        $seccion = $this->seccion->name;
        return $this->name . ' - ' . $this->competition->name. ' ['.$grado.' '.$seccion.']';
    }

    // Método para obtener el puntaje total de la sección
    public function getTotalScoreForSection($seccionId)
    {
        return $this->answers()
            ->where('seccion_id', $seccionId)
            ->sum('score');
    }

    public function getAttachmentUrlAttribute()
    {
        return ($this->attachment) ? asset('storage/educationals/'.$this->attachment) : null;
    }

    public function getFullGradoAttribute()
    {
        return $this->grado->name . '['.$this->grado->pestudio->name . ']';
    }

    public static function setActive($id)
    {
        $debates = Debate::all();
        foreach ($debates as $item) {
            $debate = Debate::find($item->id);
            $debate->status_active = ($debate->id == $id) ? true : false ;
            $debate->save();
        }
        DebateQuestion::setDesActiveAll();
        return Debate::find($id);
    }
    public static function setDesactive($id)
    {
        $debates = Debate::all();
        foreach ($debates as $item) {
            $debate = Debate::find($item->id);
            $debate->status_active = ($debate->id == $id) ? false : false ;
            $debate->save();
        }
        DebateQuestion::setDesActiveAll();
        return Debate::find($id);
    }
    public static function setDesActiveAll()
    {
        $debates = Debate::where('status_active',true)->get();
        foreach ($debates as $item) {
            $debate = Debate::find($item->id);
            $debate->status_active = false ;
            $debate->save();
        }
        DebateQuestion::setDesActiveAll();
    }

    public function getSeccionsAttribute()
    {
        return Seccion::query()
            ->select('seccions.*')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('debates', 'grados.id', '=', 'debates.grado_id')
            ->where('debates.id',$this->id)
            ->where('seccions.status_active','true')
            ->where('seccions.status_inscription_affects','true')
            ->get();
    }

    public function getPestudioAttribute()
    {
        return ($this->grado) ? $this->grado->pestudio : null;
    }

    public static function ActiveCompetitionId($CompetitionId = null)
    {
        return Debate::query()
            ->select('debates.*')
            ->where('debates.competition_id',$CompetitionId)
            ->where('debates.status_active',true)
            ->orderby('debates.created_at')
            ->first();
    }

}
