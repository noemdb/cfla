<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class MarkdownParser extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'markdown.parser';
        // o 'bootstrap.markdown' si usaste ese nombre
    }
}