<?php

namespace App\Models\app\Blog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $dates = ['created_at','updated_at','deleted_at'];

    protected $fillable = [
        'name','icon','title','rol_area_id','descriptions','color_class','order','status_navabar','status_feature','status_active','status_published'
    ];
    const COLUMN_COMMENTS = [
        ':'=>'Nombre',
        'icon'=>'Icono',
        'title'=>'Título',
        'rol_area_id'=>'Área asociada',
        'descriptions'=>'Descripción',
        'color_class'=>'Color',
        'order'=>'Orden',
        'status_navabar'=>'Barra Principal',//Disponible en el navabar del sitio
        'status_feature'=>'Destacados', //Disponible en los destacados
        'status_active'=>'Activa',
        'status_published'=>'Publicada'
    ];

    public function posts()
    {
        return $this->hasMany(Post::class,'category_id');
    }

    public function scopePublic($query)
    {
        return $query->where('categories.status_active',true)->where('categories.status_published',true);
    }    

    public static function getCategoryPublic($category_id)
    {
        return Category::public()->where('categories.id',$category_id)->first();
    }

    public function getPublicPostsAttribute()
    {
        $posts =
        Post::select('posts.*')
            ->public()
            ->pinned()
            ->where('posts.category_id',$this->id)
            ->get();

        return $posts;
    }
    public function getIconClassAttribute()
    {
        switch ($this->icon) {
            case 'control': $icon = 'document-text'; break;
            
            default:  $icon = 'document'; break;
        };

        return $icon;
    }
}
