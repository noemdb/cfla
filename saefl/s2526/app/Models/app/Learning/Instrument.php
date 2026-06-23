<?php

namespace App\Models\app\Learning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Instrument extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedagogical_id',
        'teaching_id',
        'order',
    ];
    

    const COLUMN_COMMENTS = [
        'pedagogical_id' => 'Instrumento pedagógico',
        'teaching_id' => 'Instrumento de enseñanza',
        'order' => 'Orden de aparición del instrumento pedagógico en la lección',
    ];

    public function pedagogical()
    {
        return $this->belongsTo(Pedagogical::class);
    }
    public function teaching()
    {
        return $this->belongsTo(Teaching::class);
    }
        
    public static function getAllInstruments(): Collection
    {
        return Instrument::all();
    }

    public function scopeByPedagogical($pedagogical_id)
    {
        return $this->where('pedagogical_id', $pedagogical_id);
    }
    public function scopeByTeaching($teaching_id)
    {
        return $this->where('teaching_id', $teaching_id);
    }
      

}
