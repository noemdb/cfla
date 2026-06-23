<?php

namespace App\Http\Livewire\CustomObject;

use Livewire\Wireable;

class CommunityHourAsistent implements Wireable
{
    public $items = [];
 
    public function __construct($items)
    {
        $this->items = $items;
    }
 
    public function toLivewire()
    {
        return $this->items;
    }
 
    public static function fromLivewire($value)
    {
        return new static($value);
    }
}
