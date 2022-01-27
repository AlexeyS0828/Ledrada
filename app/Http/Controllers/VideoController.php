<?php

namespace App\Http\Controllers;

use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Services\VideoService;
use FFMpeg\FFProbe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoController extends Controller
{
    public function upload(Request $request, VideoService $videoService): VideoResource
    {
        $validated = $request->validate(
            [
                'video' => 'required|file|mimes:webm,mp4',
            ]
        );

        $videoFile = $request->file('video');
        $videoName = $videoFile->getClientOriginalName();

        $uploadedFile = Storage::disk('public')->putFile('videos', $videoFile);
        $absoluteFilePath = Storage::disk('public')->path($uploadedFile);

        $video = new Video();
        $video->length = $videoService->getDuration($absoluteFilePath);
        $video->uuid = Str::uuid();
        $video->name = $videoName;
        $video->video_path = $uploadedFile;
        $video->thumbnail_path = $videoService->createThumbnail($absoluteFilePath, (int)$video->length / 2);
        $video->save();

        return new VideoResource($video);
    }
}
