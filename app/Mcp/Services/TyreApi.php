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
        // /api/tyres?filter[size]=195/55R16&filter[SEASON]=winter
        $url = $this->base . '/tyres';
        Log::info('TyreApi search', ['url' => $url, 'q' => $q]);
        return Http::get($url, $q)
            ->json();
    }

    public function details(string $id): array
    {
        return Http::get($this->base . '/tyres/' . $id)
            ->json();
    }
}
