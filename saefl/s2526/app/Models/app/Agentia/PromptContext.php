<?php

namespace App\Models\app\Agentia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromptContext extends Model
{
    use HasFactory;

    protected $table = 'prompt_contexts';

    protected $fillable = [
        'code',
        'context_objective',
        'specific_requirements',
        'content',
        'sources_precision',
        'style',
    ];

    protected $casts = [
        'code' => 'string',
        'context_objective' => 'string',
        'specific_requirements' => 'string',
        'content' => 'string',
        'sources_precision' => 'string',
        'style' => 'string',
        'created_at' => 'datetime', // Convierte created_at a Carbon
        'updated_at' => 'datetime', // Convierte updated_at a Carbon
    ];

    /**
     * Método para obtener el contexto formateado como una cadena.
     *
     * @param int $id El ID del registro a buscar.
     * @return string|null El contexto formateado o null si no se encuentra el registro.
     */
    public static function getContextForCode(String $code): ?string
    {
        // Buscar el registro por su ID
        $context = self::where('code',$code)->first(); //dd($context);

        // Si no se encuentra el registro, retornar null
        if (!$context) {
            return null;
        }

        $formattedContext = 
            "Contexto/Objetivo: ".$context->context_objective
            ."Requisitos Específicos: ".$context->specific_requirements
            ."Contenido: ".$context->content 
            ."Fuentes y Precisión: ".$context->sources_precision
            ."Estilo: ".$context->style
        ; //dd($formattedContext);

        return $formattedContext;
    }
    
}
/*

code
context_objective
specific_requirements
content
sources_precision
style
*/