<?php

namespace App\Mcp\Tools;

use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

class GetTyreDetailsTool extends Tool
{

    protected string $description = 'Get full tyre details by SKU/ID (label, noise, load/speed, warranty).';

    public function __construct(protected \App\Mcp\Services\TyreApi $api) {}

    public function schema(\Illuminate\JsonSchema\JsonSchema $schema): array
    {
        return [
            'sku' => $schema->string()->description('Tyre SKU/ID')->required(),
        ];
    }

    public function handle(\Laravel\Mcp\Request $request): \Laravel\Mcp\Response
    {
        $sku = $request->string('sku');
        $details = $this->api->details($sku);

        return $details
            ? \Laravel\Mcp\Response::json($details)
            : \Laravel\Mcp\Response::error('Tyre not found');
    }
}
