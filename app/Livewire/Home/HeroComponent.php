<?php

namespace App\Livewire\Home;

use App\Models\app\Blog\Post;
use Livewire\Component;

class HeroComponent extends Component
{
    public $post;
    public $modalShow = false;
    protected $scrollTo = 'hero';

    public function render()
    {
        $posts = Post::getPriorityPosts();
        return view('livewire.home.hero-component',[
            'posts' => $posts,
        ]);
    }

    public function showItem($id)
    {
        $this->post = Post::find($id);
        $this->modalShow = true;
    }
}
