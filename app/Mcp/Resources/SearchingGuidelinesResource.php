<?php

namespace App\Mcp\Resources;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Resource;

class SearchingGuidelinesResource extends Resource
{
    protected string $description = 'House rules: size parsing, priority on-stock';

    public function handle(Request $request): Response
    {
        return Response::text(<<<MD
# TyreStore Buying Guidelines
- Always normalize size and confirm Load index/Speed index if present.
- Prefer on-stock; if out-of-stock, show ETA.
MD);
    }
}
