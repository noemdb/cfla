<?php

namespace App\Livewire\Profesor\Lms;

use App\Services\Lms\CommentModerationService;
use Livewire\Component;

class PendingCommentCount extends Component
{
    public int $count = 0;

    protected $listeners = ['comment-approved' => '$refresh', 'comment-rejected' => '$refresh'];

    public function mount(): void
    {
        $this->refreshCount();
    }

    public function refreshCount(): void
    {
        $service = app(CommentModerationService::class, ['user' => auth()->user()]);
        $this->count = $service->countPending();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.profesor.lms.pending-comment-count');
    }
}
