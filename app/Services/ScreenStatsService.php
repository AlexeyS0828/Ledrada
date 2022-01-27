<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ScreenStatsService
{

    public function __construct()
    {
    }

    public function getScreenStats(string $screenId, int $length = 20, int $frequency = 1, $days = 28): ?array
    {
        return Cache::remember(
            "$screenId $length $frequency $days",
            86400,
            function () use (
                $screenId,
                $length,
                $frequency,
                $days
            ) {
                return $this->downloadScreenStats($screenId, $length, $frequency, $days);
            }
        );
    }

    public function downloadScreenStats(string $screenId, int $length = 20, int $frequency = 1, $days = 28): ?array
    {
        $url = "https://ledvortex.com/api/calculate-replays/{$screenId}/a45b51d426db7326bcf74b943e845e20?length={$length}&frequency={$frequency}&days={$days}";

        try {
            $screenStats = Http::get($url)->json();
        } catch (\Exception $e) {
            $screenStats = null;
        }

        return $screenStats;
    }

    public function downloadScreensInfo(): array
    {
        try {
            $decodedScreens = Http::get(
                'https://ledvortex.com/api/network-screens/a45b51d426db7326bcf74b943e845e20'
            )->json();
        } catch (\Exception $e) {
            $decodedScreens = [];
        }

        return $this->parseScreens($decodedScreens);
    }

    public function parseScreens(array $screens): array
    {
        return array_map(
            function ($screen) {
                return [
                    'id'          => $screen['screenid'] ?? null,
                    'name'        => $screen['name'] ?? null,
                    'photo'       => $this->getScreenPhoto($screen),
                    'address'     => $screen['address'] ?? null,
                    'city'        => $screen['city'] ?? null,
                    'province'    => $screen['province'] ?? null,
                    'geolocation' => $screen['geolocation'] ?? null,
                    'description' => $screen['description'] ?? null,
                ];
            },
            $screens
        );
    }

    public function getScreenPhoto($screen): ?string
    {
        if (empty($screen['photo'])) {
            return null;
        }

        $photo = $screen['photo'];

        $rootDirectory = 'screens';

        $newFilename = Str::slug($screen['name'] . ' ' . $screen['city']);

        $filePath = "public/{$rootDirectory}/{$newFilename}.jpg";

        if ($exists = Storage::exists($filePath)) {
            return url(Storage::url($filePath));
        }

        $photoContents = Http::get('https://ledvortex.com/uploads/screens/' . $photo)->body();

        if (!$photoContents) {
            return null;
        }

        Storage::put($filePath, $photoContents);

        return $newFilename;
    }
}
