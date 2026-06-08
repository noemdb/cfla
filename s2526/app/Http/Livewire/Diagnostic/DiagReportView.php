<?php

namespace App\Http\Livewire\Diagnostic;

use App\Models\app\Instrument\DiagReport;
use App\Models\app\Instrument\DiagRecommendation;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DiagReportView extends Component
{
    use AuthorizesRequests;

    public $reportId;
    public $report;
    public $showRecommendationForm = false;
    public $newRecommendation = [
        'type' => 'area',
        'recommendation' => '',
        'priority' => 'medium',
        'suggested_frequency' => 'weekly',
    ];

    public function mount($reportId)
    {
        $this->reportId = $reportId;
        $this->loadReport();
        $this->authorize('view', $this->report);
    }

    public function loadReport()
    {
        $this->report = DiagReport::with([
            'estudiant',
            'diagMain',
            'results',
            'pensumResults.pensum',
            'indicatorResults.indicator',
            'recommendations',
        ])->findOrFail($this->reportId);
    }

    public function addRecommendation()
    {
        $this->authorize('update', $this->report);

        $this->validate([
            'newRecommendation.recommendation' => 'required|min:10',
            'newRecommendation.type' => 'required|in:area,transversal,followup',
            'newRecommendation.priority' => 'required|in:low,medium,high',
        ]);

        DiagRecommendation::create([
            'report_id' => $this->report->id,
            'type' => $this->newRecommendation['type'],
            'recommendation' => $this->newRecommendation['recommendation'],
            'priority' => $this->newRecommendation['priority'],
            'suggested_frequency' => $this->newRecommendation['suggested_frequency'],
            'active' => true,
        ]);

        $this->loadReport();
        $this->showRecommendationForm = false;
        $this->reset('newRecommendation');
        session()->flash('message', 'Recomendación agregada exitosamente.');
    }

    public function validateReport()
    {
        $this->authorize('validate', $this->report);

        $this->report->update([
            'status' => 'validated',
            'validated_at' => now(),
        ]);

        $this->report->fireModelEvent('validated');
        $this->loadReport();
        session()->flash('message', 'Informe validado exitosamente.');
    }

    public function signReport()
    {
        $this->authorize('sign', $this->report);

        $this->report->update([
            'status' => 'signed',
        ]);

        $this->report->fireModelEvent('signed');
        $this->loadReport();
        session()->flash('message', 'Informe firmado exitosamente.');
    }

    public function render()
    {
        return view('livewire.diagnostic.diag-report-view');
    }
}
