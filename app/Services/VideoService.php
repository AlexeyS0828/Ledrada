<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoService
{
    protected FfmpegService $ffmpegService;

    public function __construct(FfmpegService $ffmpegService)
    {
        $this->ffmpegService = $ffmpegService;
    }

    public function getDuration(string $filePath): float
    {
        return $this->ffmpegService->getDuration($filePath);
    }

    public function createThumbnail(string $filePath, int $seconds): string
    {
        $fileName = "thumbs/" . Str::random(12) . ".jpg";
        Storage::disk('public')->put($fileName, $this->ffmpegService->getThumbnailBase64($filePath, $seconds));

        return $fileName;
    }
}
