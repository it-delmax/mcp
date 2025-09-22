<?php

use App\Mcp\Servers\TyreStoreServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp/tyrestore', TyreStoreServer::class); //->middleware('auth:sanctum');   // AI klijent šalje Authorization: Bearer <token>

Mcp::local('tyrestore', TyreStoreServer::class);
