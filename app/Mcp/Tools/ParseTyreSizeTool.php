<?php

namespace App\Mcp\Tools;

use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[IsReadOnly]
#[IsIdempotent]
class ParseTyreSizeTool extends Tool
{
    protected string $description = 'Normalizes loose tyre size text into canonical form: 195/55 R16.';

    public function schema(JsonSchema $schema): array
    {
        return [
            'input' => $schema->string()->description('User-entered size, e.g. "195 5516", "205/55r16 91v".')->required(),
        ];
    }

    public function handle(Request $request): Response
    {
        $raw = strtoupper(preg_replace('/\s+/', '', $request->string('input')));
        // Extract W/H/R (very tolerant)
        if (!preg_match('/(\d{3})[\/ ]?(\d{2})(?:R|ZR|\/)?(\d{2})/', $raw, $m)) {
            return Response::error('Could not parse tyre size. Example valid inputs: "195 55 16", "205/55R16".');
        }
        [$all, $w, $h, $r] = $m;
        // Optional load/speed index
        preg_match('/\s?(\d{2,3})([A-Z])$/', $request->string('input'), $i);
        $li = $i[1] ?? null;
        $si = $i[2] ?? null;

        return Response::json([
            'size' => sprintf('%s/%s R%s', $w, $h, $r),
            'width' => (int)$w,
            'aspect' => (int)$h,
            'rim' => (int)$r,
            'load_index' => $li,
            'speed_index' => $si,
        ]);
    }
}
