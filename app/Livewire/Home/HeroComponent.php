<?php

namespace App\Livewire\Home;

use App\Models\app\Blog\Post;
use Livewire\Component;

class HeroComponent extends Component
{
    // public $posts;

    public function render()
    {
        $posts = Post::getPriorityPosts();
        return view('livewire.home.hero-component',[
            'posts' => $posts,
        ]);
    }
}
