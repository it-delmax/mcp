<?php

namespace App\Mcp\Prompts;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Prompts\Argument;

class TyreFinderPrompt extends Prompt
{
    protected string $description = 'Guides user from vague size to order: parse size, search, details';

    public function arguments(): array
    {
        return [
            new Argument(
                name: 'locale',
                description: 'sr or en; affects tone and units if needed.',
                required: false
            ),
        ];
    }

    public function handle(Request $request): array
    {
        $loc = $request->string('locale', 'sr');
        $system = $loc === 'sr'
            ? "Ti si asistent za kupovinu guma. Prvo pozovi ParseTyreSizeTool za svaki unos veličine."
            : "You are a tyre shopping assistant. Always call ParseTyreSizeTool first.";

        return [
            Response::text($system)->asAssistant(),
            Response::text('Recite dimenziju (npr. 195 55 16)'),
        ];
    }
}
