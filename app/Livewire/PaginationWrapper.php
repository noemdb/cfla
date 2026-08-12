<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaginationWrapper extends Component
{
    public LengthAwarePaginator $paginator;

    public function render()
    {
        return view('livewire.pagination-wrapper');
    }
}