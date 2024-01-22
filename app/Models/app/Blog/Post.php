<?php

namespace App\Models\app\Blog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $dates = ['created_at','updated_at','deleted_at'];

    protected $fillable = [
        'user_id','icon','category_id','title','description','body','file_url','order','created_at','status_priority','status_feature','status_coverPage',
        'status_pinned','status_banned','status_active','status_published','status_help'
    ];

    const COLUMN_COMMENTS = [
        'user_id'=>'Autor',
        'icon'=>'Icono',
        'category_id'=>'Categoría',
        'title'=>'Título',
        'description'=>'Descripción',
        'body'=>'Texto',
        'file_url'=>'Archivo adjunto',
        'order'=>'Orden',
        'created_at'=>'Fecha de creación',
        'status_priority'=>'Prioritario',
        'status_feature'=>'Disponible en los destacados',
        'status_coverPage'=>'Disponible en la página principal',
        'status_pinned'=>'Anclado al inicio de la lista',
        'status_banned'=>'Baneado',
        'status_active'=>'Activo',
        'status_published'=>'Publicado'
    ];

    /*INI relaciones entre modelos*/
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
