<?php

namespace App\Services\Lms;

use App\Models\app\Academy\Lms\LmsMediaLibrary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LmsMediaUploadService
{
    protected string $disk = 'lms_media';

    protected array $allowedMimes = [
        'application/pdf',
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'video/mp4', 'video/webm',
        'audio/mpeg', 'audio/wav',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    protected int $maxSizeBytes = 52428800;

    public function upload(UploadedFile $file, int $uploaderId): LmsMediaLibrary
    {
        abort_if(
            !in_array($file->getMimeType(), $this->allowedMimes),
            422,
            'Tipo de archivo no permitido.'
        );

        abort_if(
            $file->getSize() > $this->maxSizeBytes,
            422,
            'El archivo supera el límite de 50 MB.'
        );

        $directory = 'lms/' . now()->format('Y/m');
        $filename  = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path      = $file->storeAs($directory, $filename, $this->disk);

        return LmsMediaLibrary::create([
            'uploaded_by'   => $uploaderId,
            'disk'          => $this->disk,
            'path'          => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'size_bytes'    => $file->getSize(),
            'provider'      => 'LOCAL',
        ]);
    }

    public function registerExternal(
        string $url,
        string $provider,
        string $title,
        int $uploaderId
    ): LmsMediaLibrary {
        return LmsMediaLibrary::create([
            'uploaded_by'   => $uploaderId,
            'disk'          => 'external',
            'path'          => '',
            'original_name' => $title,
            'mime_type'     => 'text/uri-list',
            'size_bytes'    => 0,
            'provider'      => strtoupper($provider),
            'external_url'  => $url,
        ]);
    }
}
