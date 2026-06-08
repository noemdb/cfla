<?php

namespace App\Models\app\Learning;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Teaching extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'objective',
        'author_id',
        'active',
        'url',
        'keywords',
        'curricular_area',
        'application_time',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }    
    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'instruments', 'teaching_id', 'lesson_id');
    }
    public function instruments()
    {
        return $this->hasMany(Instrument::class);
    }

    const COLUMN_COMMENTS = [
        'name' => 'Nombre del instrumento pedagógico',
        'description' => 'Descripción detallada del instrumento pedagógico',
        'type' => 'Tipo de instrumento pedagógico (Evaluación, Aprendizaje, Apoyo)', //['evaluacion', 'aprendizaje', 'apoyo']
        'objective' => 'Objetivo del instrumento pedagógico',
        'author_id' => 'ID del usuario que creó el instrumento pedagógico',
        'active' => 'Indica si el instrumento pedagógico está activo (1) o inactivo (0)',
        'url' => 'URL del instrumento pedagógico',
        'keywords' => 'Palabras clave para la búsqueda y clasificación',
        'curricular_area' => 'Área curricular a la que se asocia',
        'application_time' => 'Tiempo estimado de aplicación (minutos)',
    ];
        
    public static function getAllInstruments(): Collection
    {
        return Teaching::all();
    }

    public function scopeActive()
    {
        return $this->where('active', 1);
    }
    public function scopeByName($name)
    {
        return $this->where('name', 'like', "%$name%");
    }
    public function scopeByAuthor($author_id)
    {
        return $this->where('author_id', $author_id);
    }
    public function scopeByCurricularArea($curricular_area)
    {
        return $this->where('curricular_area', $curricular_area);
    }
         
}
