<?php

namespace App\Livewire\Home;

use App\Models\app\Blog\Post;
use Livewire\Component;

class FeaturedComponent extends Component
{
    public $posts,$post;
    public $modalShow = false;

    public function mount()
    {
        $this->posts = Post::getFeaturePosts(); //dd($this->posts);
    }


    public function render()
    {
        return view('livewire.home.featured-component');
    }

    public function showItem($id)
    {
        $this->post = Post::find($id);
        $this->modalShow = true;
    }
}
