<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewCampaignRequest;
use App\Models\Campaign;
use App\Models\Video;
use App\Services\PaymentService;
use App\Services\ScreenStatsService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CampaignController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function create(ScreenStatsService $screenStatsService, NewCampaignRequest $request)
    {
        $screensInfo = collect($screenStatsService->downloadScreensInfo());

        $data = $request->validated();
        $period = $data['days'];
        $screens = $data['screens'];

        $video = Video::where('uuid', $data['video'])->first();

        $periodDateError = false;

        foreach ($period as &$date) {
            try {
                $date = Carbon::parse($date)->startOfDay();
            } catch (\Exception $e) {
                $periodDateError = true;
                continue;
            }
        }

        if (count($period) === 0 || count($period) > 2) {
            $periodDateError = true;
        }

        $now = Carbon::now()->startOfDay();

        if ($periodDateError) {
            throw ValidationException::withMessages(['days' => 'Campaign period is invalid']);
        }

        if ($now->greaterThanOrEqualTo($period[0])) {
            throw ValidationException::withMessages(['days' => 'Campaign has to start at least tomorrow']);
        }

        if (count($period) > 1 && $period[1]->diffInDays($period[0]) < 1) {
            throw ValidationException::withMessages(['days' => 'Campaign has to last at least a day']);
        }

        $days = count($period) === 1 ? 1 : ($period[1]->diffInDayS($period[0]) + 1);

        $campaignScreensData = [];
        $videoData = $video->makeVisible(
            [
                'id',
                'video_path',
                'created_at',
                'updated_at',
            ]
        );

        $totalCost = 0;

        foreach ($screens as $screenId) {
            $campaignScreensData[$screenId] = $screenStatsService->getScreenStats(
                $screenId,
                round($video->length),
                1,
                $days
            );

            $campaignScreensData[$screenId]['screen_details'] = $screensInfo->where('id', $screenId)->first();
            $totalCost += $campaignScreensData[$screenId]['total_cost'];
        }

        $userCode = Str::upper(Str::random('6'));

        $campaign = Campaign::create(
            [
                'uuid'            => Str::uuid(),
                'code'            => $userCode,
                'email'           => $data['email'],
                'video_id'        => $video->id,
                'screens'         => $campaignScreensData,
                'video_details'   => $videoData,
                'total_price'     => $totalCost,
                'campaign_from'   => $period[0],
                'campaign_to'     => ($period[1] ?? $period[0])->endOfDay(),
                'campaign_length' => $days,
            ]
        );

        return $campaign->only(['email', 'code']);
    }

    public function show(string $email, string $code)
    {
        $campaign = Campaign::where('email', $email)->where('code', $code)->first();

        if (!$campaign) {
            abort(404);
        }

        return $campaign;
    }

    public function getPaymentStatus(PaymentService $paymentService, string $email, string $code)
    {
        $campaign = Campaign::where('email', $email)->where('code', $code)->first();

        if (!$campaign) {
            abort(404);
        }

        return ['status' => $campaign->status];
    }

    public function showAvailableCrypto(PaymentService $paymentService, string $email, string $code)
    {
        $campaign = Campaign::where('email', $email)->where('code', $code)->first();

        if (!$campaign) {
            abort(404);
        }

        return $paymentService->getAvailableCurrencies();
    }

    public function createPayment(PaymentService $paymentService, string $email, string $code)
    {
        $campaign = Campaign::where('email', $email)->where('code', $code)->first();

        if (!$campaign) {
            abort(404);
        }

        $campaignCode = "{$campaign->email}/{$campaign->code}";

        $currency = request()->post('currency', 'btc');

        $ipnUrl = route('campaignPaymentIpn', ['email' => $email, 'code' => $code]);

        return $paymentService->createPayment($campaignCode, $campaign->total_price, $currency, $ipnUrl);
    }

    public function paymentIpn(PaymentService $paymentService, string $email, string $code)
    {
        $campaign = Campaign::where('email', $email)->where('code', $code)->first();

        if (!$campaign || $campaign->status !== 'pending_payment') {
            abort(404);
        }

        $status = request('payment_status');

        if($status === 'finished' || $status === 'sending') {
            $campaign->update(['status'=>'payment_received']);
        }
    }
}
