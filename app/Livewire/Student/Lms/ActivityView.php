<?php

namespace App\Livewire\Student\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\LmsActivityLog;
use Livewire\Component;

class ActivityView extends Component
{
    public Activity $activity;
    public $sections = [];
    public $resources = [];
    public $links = [];
    public $htmlEmbeds = [];

    public function mount(Activity $activity): void
    {
        abort_unless(
            $activity->lmsPublication?->isVisibleToStudents(),
            404,
            'Esta actividad no está disponible.'
        );

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

        $this->htmlEmbeds = $activity->lmsHtmlEmbeds()
            ->where('is_visible', true)
            ->get()
            ->map(function ($embed) {
                $embed->is_mermaid = preg_match(
                    '/^(flowchart|graph|mindmap|sequenceDiagram|classDiagram|gantt|pie|stateDiagram|erDiagram|journey|gitgraph|timeline)\b/',
                    trim($embed->html_content ?? '')
                ) === 1;
                return $embed;
            });

        LmsActivityLog::record($activity->id, auth()->id(), 'VIEW');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.student.lms.activity-view')
               ->layout('student.layouts.app');
    }
}
