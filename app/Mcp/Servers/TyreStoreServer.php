<?php

namespace App\Mcp\Servers;

use App\Mcp\Prompts\TyreFinderPrompt;
use App\Mcp\Resources\SearchingGuidelinesResource;
use App\Mcp\Tools\GetTyreDetailsTool;
use App\Mcp\Tools\ParseTyreSizeTool;
use App\Mcp\Tools\SearchTyresTool;
use Laravel\Mcp\Server;

class TyreStoreServer extends Server
{
    protected string $name = 'TyreStore MCP';
    protected string $version = '1.0.0';
    protected string $instructions = <<<TXT
You assist users in finding tyres. Always:
- Normalize tyre size (e.g. "195 55 16" -> 195/55 R16).
- Prefer on-stock items; show price, stock, speed/load index.
TXT;

    protected array $tools = [
        ParseTyreSizeTool::class,
        SearchTyresTool::class,
        GetTyreDetailsTool::class
    ];

    protected array $resources = [
        SearchingGuidelinesResource::class,
    ];

    protected array $prompts = [
        TyreFinderPrompt::class,
    ];
}
