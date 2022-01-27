<?php

namespace App\Http\Controllers;

use App\Services\ScreenStatsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ScreensController extends Controller
{
    public function index(ScreenStatsService $screenStatsService): array
    {
        return $screenStatsService->downloadScreensInfo();
    }

    public function getQuota(ScreenStatsService $screenStatsService, string $screenId): ?array
    {
        $length = request('length');
        $frequency = request('frequency', 1);
        $days = request('days');

        if (!$length) {
            return ['error' => 'length not specified'];
        }

        if (!$days) {
            return ['error' => 'days not specified'];
        }

        $response = [];

        $screenIds = array_unique(array_map('trim', explode(',', $screenId)));

        foreach ($screenIds as $id) {
            $response[$id] = $screenStatsService->getScreenStats($id, $length, $frequency, $days);
        }

        return $response;
    }
}
