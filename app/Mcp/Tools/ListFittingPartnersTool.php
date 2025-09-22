<?php

namespace App\Mcp\Tools;

use App\Mcp\Services\TyreApi;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

class ListFittingPartnersTool extends Tool
{
    protected string $description = 'List nearby fitting partners for delivery & mounting.';

    public function __construct(protected \App\Mcp\Services\TyreApi $api) {}

    public function schema(\Illuminate\JsonSchema\JsonSchema $schema): array
    {
        return [
            'city'     => $schema->string()->description('City or postcode')->required(),
            'radius_km' => $schema->integer()->default(25),
            'limit'    => $schema->integer()->default(10),
        ];
    }

    public function handle(\Laravel\Mcp\Request $request): \Laravel\Mcp\Response
    {
        $partners = $this->api->partners([
            'q' => $request->string('city'),
            'radius_km' => $request->integer('radius_km', 25),
            'limit' => $request->integer('limit', 10),
        ]);

        return \Laravel\Mcp\Response::json($partners);
    }
}
