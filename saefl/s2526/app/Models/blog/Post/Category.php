<?php

namespace App\Models\blog\Post;

use App\Models\blog\Post;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];

    protected $fillable = [
        'name','rol_area_id','title','icon','descriptions','color_class','order','status_navabar','status_feature','status_active','status_published'
    ];

    const COLUMN_COMMENTS = [
        'name'=>'Nombre',
        'title'=>'Título',
        'icon'=>'Icono',
        'rol_area_id'=>'Área asociada',
        'descriptions'=>'Descripción',
        'color_class'=>'Color',
        'order'=>'Orden',
        'status_navabar'=>'Barra Principal',//Disponible en el navabar del sitio
        'status_feature'=>'Destacados', //Disponible en los destacados
        'status_active'=>'Activa',
        'status_published'=>'Publicada'
    ];

    public static function getCategoryPublic($category_id)
    {
        return Category::public()->where('categories.id',$category_id)->first();
    }

    public function rol_area()
    {
        return $this->belongsTo('App\Models\sys\RolArea');
    }

    public function posts()
    {
        return $this->hasMany('App\Models\app\Post');
    }

    public function scopePublic($query)
    {
        return $query->where('categories.status_active',true)->where('categories.status_published',true);
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

    public function getFeaturePosts($take=4)
    {
        $posts =
        Post::select('posts.*')
            ->public()
            // ->pinned()
            ->where('posts.category_id',$this->id)
            ->where('posts.status_feature',true)
            ->orderBy('posts.created_at','desc')
            ->get()
            ->take($take); //if($this->id==2) dd($posts);

        return $posts;
    }

    public static function listClassColor()
    {
        return array('primary'=>'Azul','success'=>'Verde', 'info'=>'Acuarela', 'warning'=>'Amarillo', 'danger'=>'Rojo','dark'=>'Negro','light'=>'Claro','default'=>'default');
    }

    public static function getFeatureCategories($take=4)
    {
        $categories =
            Category::select('categories.*')
            ->selectRaw('count(posts.id) as count_post')
            ->join('posts', 'categories.id', '=', 'posts.category_id')
            ->public()
            ->where('categories.status_feature',true)
            ->where('posts.status_feature',true)
            ->orderBy('categories.order')
            ->groupBy('categories.id')
            ->get()
            ->take($take);

        return $categories;

    }

    public static function getNavbarCategories($take=4)
    {
        $categories =
            Category::select('categories.*')
            ->public()
            ->where('categories.status_navabar',true)
            ->orderBy('categories.order')
            ->get()
            ->take($take);

        return $categories;

    }

    public function getcolorClassEsAttribute()
    {
        $listClassColor = Category::listClassColor();
        return ( array_key_exists($this->color_class,$listClassColor) ) ? $listClassColor[$this->color_class]:'Ninguno' ;
    }

    public function getStatusDeleteAttribute()
    {
        return ( $this->posts->count() > 0 ) ? false : true ;
    }

    public function getImageUlrAttribute()
    {

    }

    public static function list_categories()
    {
        return Category::all()->pluck('name','id') ;
    }
}
