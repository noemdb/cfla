<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\app\Academy\Lms\LmsActivityLog;
use App\Models\app\Academy\Lms\LmsActivityResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResourceDownloadController extends Controller
{
    public function download(Request $request, LmsActivityResource $resource)
    {
        abort_unless(
            $resource->activity->lmsPublication?->isVisibleToStudents(),
            404
        );

        // En vista previa (now() < publish_at) solo la 1ª sección es visible:
        // únicamente los recursos vinculados a esa sección se pueden descargar.
        $publication = $resource->activity->lmsPublication;
        if ($publication->isPreviewToStudents()) {
            $firstSectionId = $resource->activity->lmsSections()
                ->where('is_visible', true)
                ->orderBy('sort_order')
                ->limit(1)
                ->value('id');

            abort_unless($firstSectionId && $resource->section_id === $firstSectionId, 404);
        }

        abort_unless(
            $publication->allow_downloads,
            403,
            'Las descargas están deshabilitadas para esta actividad.'
        );
        abort_unless($resource->is_visible, 404);

        $media = $resource->media;
        abort_if(!$media || !$media->isLocal(), 404);

        LmsActivityLog::record(
            $resource->activity_id,
            auth()->id(),
            'RESOURCE_DOWNLOAD',
            $resource->id,
            LmsActivityResource::class
        );

        $resource->incrementDownload();

        abort_unless(Storage::disk($media->disk)->exists($media->path), 404);

        return Storage::disk($media->disk)->download(
            $media->path,
            $media->original_name
        );
    }
}
