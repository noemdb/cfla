<?php

namespace App\Http\Controllers\Diagnostic;

use App\Http\Controllers\Controller;
use App\Models\app\Instrument\DiagReport;
use App\Models\app\Instrument\DiagRecommendation;
use App\Services\Diagnostic\DiagReportComparisonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiagReportController extends Controller
{
    protected $comparisonService;

    public function __construct(DiagReportComparisonService $comparisonService)
    {
        $this->middleware('auth');
        $this->comparisonService = $comparisonService;
    }

    /**
     * Display a listing of diagnostic reports.
     */
    public function index()
    {
        $this->authorize('viewAny', DiagReport::class);

        return view('diagnostic.reports.index');
    }

    /**
     * Display the specified report.
     */
    public function show($id)
    {
        $report = DiagReport::with([
            'estudiant',
            'diagMain',
            'results',
            'pensumResults',
            'indicatorResults',
            'recommendations',
        ])->findOrFail($id);

        $this->authorize('view', $report);

        return view('diagnostic.reports.show', compact('report'));
    }

    /**
     * Approve a report (Coordinador only).
     */
    public function approve(Request $request, $id)
    {
        $report = DiagReport::findOrFail($id);
        $this->authorize('approve', $report);

        $report->update([
            'status' => 'validated',
            'validated_at' => now(),
        ]);

        // Trigger validated event for audit logging
        $report->fireModelEvent('validated');

        return redirect()->route('diagnostic.reports.show', $id)
            ->with('success', 'Informe aprobado exitosamente.');
    }

    /**
     * Sign a report (Docente only).
     */
    public function sign(Request $request, $id)
    {
        $report = DiagReport::findOrFail($id);
        $this->authorize('sign', $report);

        $report->update([
            'status' => 'signed',
        ]);

        // Trigger signed event for audit logging
        $report->fireModelEvent('signed');

        return redirect()->route('diagnostic.reports.show', $id)
            ->with('success', 'Informe firmado exitosamente.');
    }

    /**
     * Compare reports across lapsos.
     */
    public function compare(Request $request)
    {
        $estudiantId = $request->input('estudiant_id');
        $diagMainId = $request->input('diag_main_id');
        $lapsoIds = $request->input('lapso_ids', []);

        $comparison = $this->comparisonService->compareAcrossLapsos(
            $estudiantId,
            $diagMainId,
            $lapsoIds
        );

        return view('diagnostic.reports.compare', compact('comparison'));
    }
}
