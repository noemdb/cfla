<?php

namespace App\Http\Controllers\Administracion\Tab\Blogs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PostController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('administracion.blogs.posts.index');
    }

}
