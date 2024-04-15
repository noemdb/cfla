<?php

namespace App\Livewire\Home;

use App\Models\app\Blog\Post;
use Livewire\Component;
use Livewire\WithPagination;

class FeaturedComponent extends Component
{
    use WithPagination;

    public $post;
    public $modalShow = false;
    protected $scrollTo = 'feature';

    public function mount()
    {
        // $this->posts = Post::getFeaturePosts(); //dd($this->posts);
    }


    public function render()
    {
        return view('livewire.home.featured-component',[
            'posts' => Post::select('posts.*')
            ->pinned()
            ->public()
            ->where('posts.status_feature',true)
            ->paginate(5, ['*'], 'featuredPage'),
        ]);
    }

    public function showItem($id)
    {
        $this->post = Post::find($id);
        $this->modalShow = true;
    }
}
