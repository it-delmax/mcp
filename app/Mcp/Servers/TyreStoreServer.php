<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\AddToCartTool;
use App\Mcp\Tools\CreateOrderTool;
use App\Mcp\Tools\GetTyreDetailsTool;
use App\Mcp\Tools\ListFittingPartnersTool;
use App\Mcp\Tools\ParseTyreSizeTool;
use App\Mcp\Tools\SearchTyresTool;
use Laravel\Mcp\Server;

class TyreStoreServer extends Server
{
    protected string $name = 'TyreStore MCP';
    protected string $version = '1.0.0';
    protected string $instructions = <<<TXT
You assist users in buying tyres. Always:
- Normalize tyre size (e.g. "195 55 16" -> 195/55 R16).
- Prefer in-stock items; show price, stock, speed/load index.
- Offer fitting partners near user's city/postcode.
- Never place an order without explicit confirmation.
TXT;

    protected array $tools = [
        ParseTyreSizeTool::class,
        SearchTyresTool::class,
        GetTyreDetailsTool::class,
        ListFittingPartnersTool::class,
        AddToCartTool::class,
        CreateOrderTool::class,
    ];

    protected array $resources = [
        \App\Mcp\Resources\BuyingGuidelinesResource::class,
    ];

    protected array $prompts = [
        \App\Mcp\Prompts\TyreFinderPrompt::class,
    ];
}
