<?php

namespace App\Mcp\Tools;

use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Illuminate\JsonSchema\JsonSchema;

#[IsIdempotent]
class AddToCartTool extends Tool
{
    protected string $description = 'Add tyre to cart with quantity and (optional) chosen partner.';

    public function __construct(protected \App\Mcp\Services\TyreApi $api) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'cart_id' => $schema->string()->description('Existing cart id or new.')->default((string) Str::ulid()),
            'sku'     => $schema->string()->required(),
            'qty'     => $schema->integer()->min(1)->default(4),
            'partner_id' => $schema->string()->description('Optional fitting partner id'),
        ];
    }

    public function handle(\Laravel\Mcp\Request $request): \Laravel\Mcp\Response
    {
        $payload = $request->validate([
            'cart_id' => 'required|string',
            'sku'     => 'required|string',
            'qty'     => 'required|integer|min:1|max:20',
            'partner_id' => 'nullable|string',
        ]);

        $res = $this->api->addToCart($payload);

        return \Laravel\Mcp\Response::json($res);
    }
}
