<?php

namespace App\Livewire\Planning\Lms;

use App\Models\app\Academy\Activity;
use Livewire\Component;

class LessonPendingCount extends Component
{
    public int $count = 0;

    protected $listeners = [
        'lesson-scheduled' => '$refreshCount',
        'lesson-published' => '$refreshCount',
        'lesson-approved'  => '$refreshCount',
    ];

    protected function getListeners(): array
    {
        return [
            'lesson-scheduled' => '$refreshCount',
            'lesson-published' => '$refreshCount',
            'lesson-approved'  => '$refreshCount',
            'echo-private:App.Models.User.'.auth()->id() => 'refreshCountFromEcho',
        ];
    }

    public function refreshCountFromEcho(): void
    {
        $this->refreshCount();
    }

    public function mount(): void
    {
        $this->refreshCount();
    }

    public function refreshCount(): void
    {
        $this->count = Activity::whereHas('lmsPublication', fn ($q) => $q->where('status', 'SCHEDULED'))->count();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.planning.lms.lesson-pending-count');
    }
}
