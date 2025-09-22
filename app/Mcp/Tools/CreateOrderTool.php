<?php

namespace App\Mcp\Tools;

use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

class CreateOrderTool extends Tool
{
    protected string $description = 'Create order from cart (billing/shipping, payment_intent=create).';

    public function __construct(protected \App\Mcp\Services\TyreApi $api) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'cart_id' => $schema->string()->required(),
            'customer' => $schema->object([
                'email' => $schema->string()->required(),
                'name'  => $schema->string()->required(),
                'phone' => $schema->string(),
            ])->required(),
            'shipping' => $schema->object([
                'method' => $schema->string()->enum(['pickup_partner', 'home_delivery'])->required(),
                'address' => $schema->string()->description('Required if home_delivery'),
                'partner_id' => $schema->string()->description('Required if pickup_partner'),
            ])->required(),
            'payment' => $schema->object([
                'provider' => $schema->string()->enum(['stripe', 'wspay', 'cod'])->default('cod'),
            ])->required(),
        ];
    }

    public function handle(\Laravel\Mcp\Request $request): \Laravel\Mcp\Response
    {
        $validated = $request->validate([
            'cart_id' => 'required|string',
            'customer.email' => 'required|email',
            'customer.name'  => 'required|string',
            'customer.phone' => 'nullable|string',
            'shipping.method' => 'required|in:pickup_partner,home_delivery',
            'shipping.address' => 'required_if:shipping.method,home_delivery|nullable|string',
            'shipping.partner_id' => 'required_if:shipping.method,pickup_partner|nullable|string',
            'payment.provider' => 'required|in:stripe,wspay,cod',
        ], [
            'shipping.address.required_if' => 'Address is required for home delivery.',
            'shipping.partner_id.required_if' => 'Partner is required for pickup.',
        ]);

        $order = $this->api->createOrder($validated);

        return \Laravel\Mcp\Response::json($order);
    }

    // Primer: dostupno samo prijavljenim partnerima/kupcima:
    public function shouldRegister(\Laravel\Mcp\Request $request): bool
    {
        return (bool) $request?->user();
    }
}
