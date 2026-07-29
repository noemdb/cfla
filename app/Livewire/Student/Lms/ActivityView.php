<?php

namespace App\Livewire\Student\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\ActivityComment;
use App\Models\app\Academy\Lms\LmsActivityLog;
use Livewire\Component;

class ActivityView extends Component
{
    public Activity $activity;
    public $sections = [];
    public $resources = [];
    public $links = [];
    public $htmlEmbeds = [];
    public $comments;
    public string $newComment = '';
    public $completed = false;

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

        $this->comments = ActivityComment::with('user')
            ->forActivity($activity->id)
            ->approved()
            ->orderBy('created_at', 'desc')
            ->get();

        $this->completed = LmsActivityLog::where('activity_id', $activity->id)
            ->where('user_id', auth()->id())
            ->where('event', 'COMPLETE')
            ->exists();

        LmsActivityLog::record($activity->id, auth()->id(), 'VIEW');
    }

    public function markComplete(): void
    {
        LmsActivityLog::record($this->activity->id, auth()->id(), 'COMPLETE');
        $this->completed = true;

        $this->notification()->success(
            title: '¡Actividad completada!',
            description: 'Has marcado esta actividad como completada.'
        );
    }

    public function saveComment(): void
    {
        $this->validate(['newComment' => 'required|string|min:1|max:1000']);

        ActivityComment::create([
            'activity_id' => $this->activity->id,
            'user_id'     => auth()->id(),
            'body'        => $this->newComment,
            'is_approved' => false,
        ]);

        $this->newComment = '';

        $this->notification()->success(
            title: 'Comentario enviado',
            description: 'Tu comentario será visible una vez aprobado.'
        );
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.student.lms.activity-view')
               ->layout('student.layouts.app');
    }
}
