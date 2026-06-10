<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_media_library', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->string('disk', 50)->default('public');
            $table->string('path', 1000);
            $table->string('original_name');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->unsignedInteger('duration_secs')->nullable();
            $table->string('thumbnail_path', 1000)->nullable();
            $table->enum('provider', ['LOCAL', 'YOUTUBE', 'VIMEO', 'DRIVE', 'DROPBOX'])->default('LOCAL');
            $table->string('external_url', 1000)->nullable();
            $table->json('metadata')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_media_library');
    }
};
