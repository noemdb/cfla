<?php

namespace App\Http\Controllers\Admin;

use App\Models\BinnacleEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class BinnacleExportController
{
    /**
     * Exporta la bitácora (con los mismos filtros del panel) a CSV.
     * Límite defensivo de 10.000 filas; el archivado (binnacle:archive) es la
     * vía prevista para volúmenes mayores.
     */
    public function __invoke(Request $request)
    {
        $entries = BinnacleEntry::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $needle = '%'.$request->string('search')->trim().'%';
                $q->where(function ($sub) use ($needle) {
                    $sub->where('title', 'like', $needle)
                        ->orWhere('description', 'like', $needle)
                        ->orWhere('subject_identifier', 'like', $needle)
                        ->orWhere('object_identifier', 'like', $needle)
                        ->orWhere('ip_address', 'like', $needle);
                });
            })
            ->when($request->filled('category'), fn ($q) => $q->where('event_category', $request->string('category')))
            ->when($request->filled('severity'), fn ($q) => $q->where('event_severity', $request->string('severity')))
            ->when($request->filled('from'), fn ($q) => $q->where('created_at', '>=', $request->string('from').' 00:00:00'))
            ->when($request->filled('to'), fn ($q) => $q->where('created_at', '<=', $request->string('to').' 23:59:59'))
            ->orderByDesc('created_at')
            ->limit(10000)
            ->get();

        $filename = 'binnacle-'.now()->format('Ymd-His').'.csv';

        return Response::streamDownload(function () use ($entries) {
            $out = fopen('php://output', 'w');

            // BOM para Excel.
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'id', 'fecha', 'categoria', 'severidad', 'evento', 'titulo',
                'descripcion', 'usuario', 'objeto', 'ip', 'metodo', 'url',
                'request_id', 'session_id', 'created_by',
            ]);

            foreach ($entries as $entry) {
                fputcsv($out, [
                    $entry->id,
                    $entry->created_at?->format('Y-m-d H:i:s'),
                    $entry->event_category,
                    $entry->event_severity,
                    $entry->event_type,
                    Str::replace([';', "\n", "\r"], ' ', (string) $entry->title),
                    Str::replace([';', "\n", "\r"], ' ', (string) $entry->description),
                    $entry->subject_identifier,
                    $entry->object_identifier,
                    $entry->ip_address,
                    $entry->request_method,
                    $entry->request_url,
                    $entry->request_id,
                    $entry->session_id,
                    $entry->created_by,
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
