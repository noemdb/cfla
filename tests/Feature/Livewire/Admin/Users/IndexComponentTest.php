<?php

namespace Tests\Feature\Livewire\Admin\Users;

use App\Livewire\Admin\Users\IndexComponent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class IndexComponentTest extends TestCase
{
    use DatabaseTransactions;

    public function test_renders_successfully()
    {
        Livewire::test(IndexComponent::class)
            ->assertStatus(200);
    }
}
