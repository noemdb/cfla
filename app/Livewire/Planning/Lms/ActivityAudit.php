<?php

namespace App\Livewire\Planning\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\LmsActivityLog;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityAudit extends Component
{
    use WithPagination;

    public Activity $activity;

    public string $filterEvent = '';
    public string $dateFrom = '';
    public string $dateTo = '';

    public function mount(Activity $activity): void
    {
        $this->activity = $activity;
    }

    public function render(): \Illuminate\View\View
    {
        $query = LmsActivityLog::with('user')
            ->where('activity_id', $this->activity->id);

        if ($this->filterEvent) {
            $query->where('event', $this->filterEvent);
        }
        if ($this->dateFrom) {
            $query->where('created_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->where('created_at', '<=', $this->dateTo . ' 23:59:59');
        }

        return view('livewire.planning.lms.activity-audit', [
            'logs' => $query->latest('created_at')->paginate(50),
            'eventCounts' => LmsActivityLog::where('activity_id', $this->activity->id)
                ->selectRaw('event, COUNT(*) as total')
                ->groupBy('event')
                ->pluck('total', 'event'),
        ])->layout('planning.layouts.app');
    }
}
