<?php

namespace App\Models\app\Learning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class Pedagogical extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'instructions',
        'observations',
    ];

    const COLUMN_COMMENTS = [
        'lesson_id' => 'ID de la lección',
        'instructions' => 'Instrucciones para la aplicación del instrumento pedagógico en la lección',
        'observations' => 'Observaciones sobre la aplicación del instrumento pedagógico en la lección',
        'created_by' => 'Usuario que creó el registro',
        'updated_by' => 'Usuario que modificó la última vez el registro',
    ];
        
    

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
    public function instruments()
    {
        return $this->hasMany(Instrument::class);
    }
    
    //Leer
    public static function getAllPedagogicals(): Collection
    {
        return Pedagogical::all();
    }


    //Observers
    /*public function creating(Model $model)
    {
        $model->created_by = Auth::user()->id;
    }

    public function updating(Model $model)
    {
        $model->updated_by = Auth::user()->id;
    }
    */
    
    
    
}
