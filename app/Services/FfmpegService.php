<?php

namespace App\Services;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;

final class FfmpegService
{
    protected FFProbe $ffProbe;
    protected FFMpeg $ffMpeg;

    public function __construct()
    {
        $this->ffProbe = FFProbe::create($this->getConfig());
        $this->ffMpeg = FFMpeg::create($this->getConfig());
    }

    protected function getConfig(): array
    {
        return [
            'ffmpeg.binaries'  => config('ffmpeg.ffmpeg_binary'),
            'ffprobe.binaries' => config('ffmpeg.ffprobe_binary'),
        ];
    }

    public function getDuration(string $videoPath): float
    {
        return (float)$this->ffProbe->format($videoPath)->get('duration');
    }

    public function getThumbnailBase64(string $videoPath, int $seconds = 2): string
    {
        return (string)$this->ffMpeg->open($videoPath)->frame(TimeCode::fromSeconds($seconds))->save('.', false, true);
    }
}
