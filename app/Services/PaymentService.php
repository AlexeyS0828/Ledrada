<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaymentService
{
    public ?string $apiKey;
    public ?string $secretKey;

    public function __construct()
    {
        $this->apiKey = config('nowpayments.api_key');
        $this->secretKey = config('nowpayments.secret_key');
    }

    public function getAvailableCurrencies()
    {
        return Http::withHeaders(
            [
                'x-api-key' => $this->apiKey,
            ]
        )->get('https://api.nowpayments.io/v1/currencies')->body();
    }

    public function createPayment(string $orderId, float $amount, string $currency = 'btc', ?string $ipnUrl = null)
    {
        $payment = Http::withHeaders(
            [
                'x-api-key' => $this->apiKey,
            ]
        )->post(
            'https://api.nowpayments.io/v1/payment',
            [
                'price_amount'                                         => $amount,
                'price_currency'                                       => 'usd',
                'pay_currency'                                         => $currency,
                'order_id'                                             => $orderId,
                $ipnUrl ? 'ipn_callback_url' : 'ipn_callback_url_null' => $ipnUrl,
                'order_description'                                    => 'LEDRadar Campaign - ' . $orderId,
            ]
        )->json();

        return collect($payment)->only(
            [
                'pay_amount',
                'pay_currency',
                'pay_address',
                'payin_extra_id',
            ]
        );
    }
}
