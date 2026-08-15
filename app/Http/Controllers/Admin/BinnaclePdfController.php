<?php

namespace App\Http\Controllers\Admin;

use App\Models\BinnacleEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class BinnaclePdfController
{
    /**
     * Exporta la bitácora (con los mismos filtros del panel) a PDF.
     * Límite defensivo de 2.000 filas para mantener el tamaño razonable.
     */
    public function __invoke(Request $request)
    {
        $entries = BinnacleEntry::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $needle = '%'.$request->string('search')->trim().'%';
                $q->where(fn ($sub) => $sub
                    ->where('title', 'like', $needle)
                    ->orWhere('description', 'like', $needle)
                    ->orWhere('subject_identifier', 'like', $needle)
                    ->orWhere('object_identifier', 'like', $needle)
                    ->orWhere('ip_address', 'like', $needle));
            })
            ->when($request->filled('category'), fn ($q) => $q->where('event_category', $request->string('category')))
            ->when($request->filled('severity'), fn ($q) => $q->where('event_severity', $request->string('severity')))
            ->when($request->filled('from'), fn ($q) => $q->where('created_at', '>=', $request->string('from').' 00:00:00'))
            ->when($request->filled('to'), fn ($q) => $q->where('created_at', '<=', $request->string('to').' 23:59:59'))
            ->orderByDesc('created_at')
            ->limit(2000)
            ->get();

        $title = 'Bitácora de Eventos — '.now()->format('d/m/Y H:i');

        $pdf = Pdf::loadView('admin.binnacle.export-pdf', [
            'title' => $title,
            'entries' => $entries,
            'generatedAt' => now()->format('d/m/Y H:i:s'),
            'total' => $entries->count(),
        ]);

        return $pdf->download('binnacle-'.now()->format('Ymd-His').'.pdf');
    }
}
