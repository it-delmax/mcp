<?php

namespace App\Mcp\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TyreApi
{
    public function __construct(
        protected string $base = '', // prilagodi
    ) {
        if (empty($this->base)) {
            $this->base = config('mcp.api.url');
        }

        // Http::macro('withTyreApiToken', function () {
        //     return Http::withToken(config('mcp.api.token'));
        // });
    }

    public function search(array $q): array
    {
        $host = 'etg.dmx.rs';
        // /api/tyres?filter[size]=195/55R16&filter[SEASON]=winter
        $url = 'https://{$host}/api/tyres/v1' . '/tyres';
        Log::info('TyreApi search', ['url' => $url, 'q' => $q]);
        return Http::withOptions([
            'curl' => [
                // mapiraj etg.dmx.rs:443 na 127.0.0.1 → lokalni Nginx, ispravan cert kroz SNI
                CURLOPT_RESOLVE => ["{$host}:443:127.0.0.1"],
            ],
            // (opciono) ako želiš striktno IPv4 bez RESOLVE-a:
            // 'force_ip_resolve' => 'v4',
        ])->get($url, $q)
            ->json();
    }

    public function details(string $id): array
    {
        return Http::get($this->base . '/tyres/' . $id)
            ->json();
    }
}
