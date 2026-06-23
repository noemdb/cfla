<?php

namespace App\Http\Livewire\Administracion\Blog;

use App\Models\blog\Post;
use App\Models\blog\Post\Category;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class PostComponent extends Component
{
    use WithFileUploads;
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public Post $post;
    public $categories,$image;

    public $modeIndex,$modeShow,$modeEdit,$modeCreate;
    public $list_comment,$list_icon,$list_categories;

    public function mount()
    {
        $this->resetMode();
        $this->modeIndex = true;
        $this->categories = Category::all();
        $this->list_categories = Category::list_categories();
        $this->list_comment = Post::COLUMN_COMMENTS;
        $this->list_icon = Post::listIcon();
    }

    public function render()
    {
        $posts = Post::orderBy('created_at','desc')->paginate();
        return view('livewire.administracion.blog.post-component',[
            'posts'=>$posts,
        ]);
    }

    public function savePost()
    {        
        $this->post->user_id = Auth::id();
        $this->uploadImage();
        $this->validate(); //dd($this->post);
        $this->post->save();
        session()->flash('message', 'Post guardado exitosamente.');        
        $this->resetMode();
        $this->modeIndex = true;
    }

    public function uploadImage()
    {
        $this->validate([
            'image' => 'nullable|image|max:1024', // 1MB Max
        ]);
        $this->post->file_url = ($this->image) ? 'storage/posts/'.$this->image->store('images','posts') : $this->post->file_url;
    }

    public function edit($id)
    {        
        $this->resetMode();
        $this->modeEdit = true;
        $this->post = Post::findOrFail($id);
    }

    public function delete($id)
    {        
        $this->post = Post::findOrFail($id);
        $this->post->delete();
        session()->flash('message', 'Post eliminado exitosamente.');        
        $this->resetMode();
        $this->modeIndex = true;
    }

    public function create()
    {
        $this->resetMode();
        $this->modeCreate = true;        
    }

    public function resetMode()
    {
        $this->modeIndex = false;
        $this->modeShow = false;
        $this->modeEdit = false;
        $this->modeCreate = false;
        $this->image = null;
        $this->post = New Post;
        $this->resetErrorBag();
    }

    protected $rules = [
        'post.user_id' => 'required|integer',
        'post.icon' => 'required|string',
        'post.category_id' => 'required|integer',
        'post.title' => 'required|string|max:191',
        'post.description' => 'nullable|string|max:191',
        'post.body' => 'required|string',
        'post.insert' => 'nullable|string',
        'post.file_url' => 'nullable|string|max:191',
        'post.order' => 'nullable|integer',
        'post.status_priority' => 'nullable|boolean',
        'post.status_feature' => 'nullable|boolean',
        'post.status_coverPage' => 'nullable|boolean',
        'post.status_pinned' => 'nullable|boolean',
        'post.status_banned' => 'nullable|boolean',
        'post.status_active' => 'nullable|boolean',
        'post.status_published' => 'nullable|boolean',
        'post.status_help' => 'nullable|boolean',
        'post.created_at' => 'nullable|date',
        'post.updated_at' => 'nullable|date',
    ];

    public function close()
    {
        $this->resetMode();
        $this->modeIndex = true;        
    }
}
