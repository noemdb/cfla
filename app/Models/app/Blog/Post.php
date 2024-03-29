<?php

namespace App\Models\app\Blog;

use App\Models\User;
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
        return $this->belongsTo(User::class,'user_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }
    public function postfiles()
    {
        return $this->hasMany(PostFile::class, 'postfile_id');
    }
    /*FIN relaciones entre modelos*/

    public static function getPriorityPosts($take=4)
    {
        $postsPriorities =
            Post::select('posts.*')
                ->pinned()
                ->public()
                ->where('posts.status_priority',true)
                ->get()
                ->take($take);
        return $postsPriorities;
    }

    public static function getFeaturePosts($take=4)
    {
        $postsPriorities =
            Post::select('posts.*')
                ->pinned()
                ->public()
                ->where('posts.status_feature',true)
                ->get()
                ->take($take);
        return $postsPriorities;
    }

    public function scopePinned($query)
    {
        return $query->orderByRaw('ISNULL(posts.order), posts.order ASC')->orderBy('posts.created_at','desc');
    }

    public function scopePublic($query)
    {
        $result = $query
        ->join('categories', 'categories.id', '=', 'posts.category_id')
        ->where('posts.status_active',true)
        ->where('posts.status_published',true)
        ->where('categories.status_active',true)
        ->where('categories.status_published',true);
        return $result;
    }

}
