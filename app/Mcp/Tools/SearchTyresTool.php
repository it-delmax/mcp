<?php

namespace App\Mcp\Tools;

use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use App\Mcp\Services\TyreApi;

class SearchTyresTool extends Tool
{
    protected string $description = 'Pretraga guma (size, season, vehicle_type, in_stock, proizvođač, cena, paginacija).';

    public function __construct(protected TyreApi $api) {}

    /**
     * INPUT schema za AI klijenta
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'size'          => $schema->string()->description('npr. "195/55 R16"'),
            'season'        => $schema->string()->description('npr. "zimska", "letnja", "all season"'),
            'vehicle_type'  => $schema->string()->description('npr. "Putničko", "SUV", "Kombi"'),
            'manufacturer'  => $schema->string(),
            'in_stock'      => $schema->boolean()->default(false)->description('true = samo artikli sa lagerom'),
            'price_min'     => $schema->number(),
            'price_max'     => $schema->number(),
            'sort'          => $schema->string()->description('polje za sortiranje, npr. "price_with_tax" ili "-price_with_tax"'),
            'per_page'      => $schema->integer()->default(20)->min(1)->max(100),
            'cursor'        => $schema->string()->description('cursor za sledeću stranu (ako koristiš cursor paginaciju)'),
        ];
    }

    /**
     * Handle
     */
    public function handle(Request $request): Response
    {
        // Validacija – lagana i tolerantna na velika/mala slova
        $validated = $request->validate([
            'size'          => 'nullable|string|max:30',
            'season'        => 'nullable|string|max:20',
            'vehicle_type'  => 'nullable|string|max:30',
            'manufacturer'  => 'nullable|string|max:50',
            'in_stock'      => 'boolean',
            'price_min'     => 'nullable|numeric|min:0',
            'price_max'     => 'nullable|numeric|min:0',
            'sort'          => 'nullable|string|max:50',
            'per_page'      => 'integer|min:1|max:100',
            'cursor'        => 'nullable|string|max:255',
        ], [
            'size.*' => 'Parametar "size" npr. 195/55 R16.',
        ]);

        // Mapiranje na query koji tvoj API očekuje: filter[...]
        $q = [];

        if (!empty($validated['size'])) {
            $q['filter[size]'] = trim($validated['size']);
        }

        if (!empty($validated['season'])) {
            // tvoj API očekuje mala slova ("zimska", "letnja"...)
            $q['filter[season]'] = mb_strtolower(trim($validated['season']));
        }

        if (!empty($validated['vehicle_type'])) {
            // kod tebe je "Putničko" – prosleđujemo tačan string
            $q['filter[vehicle_type]'] = trim($validated['vehicle_type']);
        }

        if (!empty($validated['manufacturer'])) {
            $q['filter[manufacturer]'] = trim($validated['manufacturer']);
        }

        // lager
        if (array_key_exists('in_stock', $validated)) {
            $q['filter[in_stock]'] = $validated['in_stock'] ? 1 : 0;
        }

        // cena
        if (!empty($validated['price_min'])) $q['filter[price_min]'] = $validated['price_min'];
        if (!empty($validated['price_max'])) $q['filter[price_max]'] = $validated['price_max'];

        // sortiranje
        if (!empty($validated['sort'])) {
            // ako stigne "-price_with_tax", prosledi kao što je
            $q['sort'] = $validated['sort'];
        }

        // paginacija
        $q['per_page'] = $validated['per_page'] ?? 20;
        if (!empty($validated['cursor'])) {
            $q['cursor'] = $validated['cursor'];
        }

        // Poziv API-ja
        $res = $this->api->search($q);

        // (opciono) zaštita da images uvek bude niz (ako je null)
        if (isset($res['data']) && is_array($res['data'])) {
            foreach ($res['data'] as &$item) {
                if (!isset($item['images']) || $item['images'] === null) {
                    $item['images'] = [];
                }
            }
        }

        // Vrati “as-is” ceo payload (data, links, meta)
        return Response::json($res);
    }
}
