<?php

namespace App\Models\blog;

use App\Models\blog\Post\Category;
use App\Models\blog\Post\PostFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Jenssegers\Date\Date;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];

    protected $fillable = [
        'user_id','icon','category_id','title','description','body','file_url','order','created_at','status_priority','status_feature','status_coverPage',
        'status_pinned','status_banned','status_active','status_published','status_help','created_at','updated_at'
    ];

    const COLUMN_COMMENTS = [
        'user_id'=>'Autor',
        'icon'=>'Icono',
        'category_id'=>'Categoría',
        'title'=>'Título',
        'description'=>'Descripción',
        'body'=>'Texto',
        'file_url'=>'Archivo adjunto',
        'insert'=>'Insert',
        'order'=>'Orden',
        'created_at'=>'Fecha de creación',
        'updated_at'=>'Fecha de actualización',
        'status_priority'=>'Prioritario',
        'status_feature'=>'Disponible en los destacados',
        'status_coverPage'=>'Disponible en la página principal',
        'status_pinned'=>'Anclado al inicio de la lista',
        'status_banned'=>'Baneado',
        'status_active'=>'Activo',
        'status_published'=>'Publicado',
        'status_help'=>'Ayuda',
    ];

    public static function listIcon()
    {
        return [
            'primary'=>'primary','secondary'=>'secondary','info'=>'info','success'=>'success','waring'=>'waring','danger'=>'danger','dark'=>'dark','light'=>'light'
        ];
    }

    /*INI relaciones entre modelos*/
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function category()
    {
        return $this->belongsTo('App\Models\blog\Post\Category');
    }
    public function postfiles()
    {
        return $this->belongsTo('App\Models\blog\Post\PostFile');
    }
    /*FIN relaciones entre modelos*/

    public function getColorClassAttribute()
    {
        return ($this->category) ? $this->category->color_class: null;
    }

    public function getCreatedDiffForHumansAttribute()
    {
        return ($this->created_at) ? $this->created_at->diffForHumans():$this->created_at;
    }
    public function getUpdatedDiffForHumansAttribute()
    {
        return ($this->updated_at) ? $this->updated_at->diffForHumans():$this->updated_at;
    }

    public function getDateAttribute()
    {
        return ($this->updated_at) ? $this->updated_at->format('d-m-Y'):$this->created_at->format('d-m-Y');
    }

    public function getOnLineAttribute()
    {
        $post = Post::select('posts.*')->public()->where('posts.id',$this->id)->first();
        return ($post) ? true:false;
    }

    public function getUrlPathAttribute()
    {
        $exist = Storage::disk('posts')->exists($this->file_url);
        $file_url = ($exist) ? Storage::url($this->file_url) : null ; //dd($this,$this->file_url,$exist,$file_url);
        return $file_url;
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
        $posts = Post::select('posts.*')
            ->pinned()
            ->public()
            ->where('posts.status_feature',true)
            ->get()
            ->take($take);//dd($posts);
        return $posts;
    }

    public static function getCoverPagePosts($take=6)
    {
        $posts =
        Post::select('posts.*')
            ->public()
            ->pinned()
            ->where('status_coverPage',true)
            ->get()
            ->take($take);

        return $posts;
    }

    public static function getPostsCategoyPublic($category_id)
    {
        $posts = Post::select('posts.*')->public()->pinned();

        $category = Category::public()->where('categories.id',$category_id)->first();

        $posts = ($category) ? $posts->where('posts.category_id',$category->id) : $posts ;

        $posts = $posts->get();

        return $posts;
    }

    public static function getPostPublic($post_id)
    {
        return Post::public()->where('posts.id',$post_id)->first();
    }

    public static function getYears()
    {
        $years = Post::select(DB::raw('YEAR(posts.created_at) as year'))
        ->public()
        ->whereNotNull('posts.created_at')
        ->groupBy('year')
        ->pluck('year');
        return $years;
    }

    public static function getMonthsForYear($year='2000')
    {
        $months = Post::select('posts.*')
        ->select(DB::raw('MONTH(posts.created_at) as month'))
        ->public()
        ->whereYear('posts.created_at', '=',$year)
        ->groupBy('month')
        ->pluck('month');
        return $months;
    }

    public static function getHistoryPosts()
    {
        $years = Post::getYears();
        $datas = collect();

        foreach ($years as $year) {
            $data = collect();
            $months = Post::getMonthsForYear($year);

            foreach ($months as $month) {
                $posts = Post::select('posts.*')
                ->public()
                ->whereYear('posts.created_at', '=',$year)
                ->whereMonth('posts.created_at', '=',$month)
                ->get();
                $monthName = ucfirst(Date::createFromFormat('!m', $month)->format('F'));
                $data->put($monthName,$posts);
            }
            $datas->put($year,$data);
        }
        return $datas;
    }

    public function getAttachmentsAttribute()
    {
        $attachments = PostFile::where('post_id',$this->id)->get();
        return $attachments;
    }
}
