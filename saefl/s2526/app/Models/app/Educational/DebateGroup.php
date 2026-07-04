<?php

namespace App\Models\app\Educational;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebateGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id',
        'name',
        'description',
        'attachment',
    ];

    const COLUMN_COMMENTS = [
        'competition_id' => 'Competición',
        'name' => 'Nombre',
        'description' => 'Descripción',
        'attachment' => 'Archivo adjunto'
    ];

    public function answers() { return $this->hasMany(DebateAnswer::class,'group_id'); }
    public function competition() { return $this->belongsTo(DebateCompetition::class,'competition_id'); }

    public function getTotalScoreForSection($groupId)
    {
        return $this->answers()
            ->where('group_id', $groupId)
            ->sum('score');
    }
}
