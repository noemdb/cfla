<?php

/* resource */

use App\Http\Controllers\Administracion\Tab\Blogs\PostController;

Route::get('/blogs/posts/index', [PostController::class,"index"])->name('administracion.blogs.posts.index');