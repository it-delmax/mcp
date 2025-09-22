<?php

namespace App\Mcp\Resources;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Resource;

class BuyingGuidelinesResource extends Resource
{
    protected string $description = 'House rules: size parsing, priority in-stock, partner pickup vs delivery, etc.';

    public function handle(Request $request): Response
    {
        return Response::text(<<<MD
# TyreStore Buying Guidelines
- Always normalize size and confirm LI/SI if present.
- Prefer in-stock; if out-of-stock, show ETA.
MD);
    }
}
