<?php

namespace Tests\Feature\Livewire\Admin\Users;

use App\Livewire\Admin\Users\IndexComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class IndexComponentTest extends TestCase
{
    public function test_renders_successfully()
    {
        Livewire::test(IndexComponent::class)
            ->assertStatus(200);
    }
}
