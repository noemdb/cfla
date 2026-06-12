<?php

namespace App\Livewire\Planning\Lms;

use App\Models\app\Academy\Activity;
use Livewire\Component;

class LmsLessonViewer extends Component
{
    public Activity $activity;
    public $sections = [];
    public $resources = [];
    public $links = [];

    public function mount(Activity $activity): void
    {
        $this->activity = $activity;

        $this->sections = $activity->lmsSections()
            ->where('is_visible', true)
            ->with(['visibleContents.media'])
            ->get();

        $this->resources = $activity->lmsResources()
            ->where('is_visible', true)
            ->with('media')
            ->get();

        $this->links = $activity->lmsLinks()
            ->where('is_visible', true)
            ->get();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.planning.lms.lesson-viewer', [
            'publication' => $this->activity->lmsPublication,
        ])->layout('planning.layouts.app');
    }
}
