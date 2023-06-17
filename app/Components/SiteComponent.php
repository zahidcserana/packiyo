<?php

namespace App\Components;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Webpatser\Countries\Countries;

class SiteComponent extends BaseComponent
{
    public function filterCountries(Request $request): JsonResponse
    {
        $term = "%{$request->get('term')}%";

        $results = Countries::where('name', 'LIKE', $term);

        if ($results->count() === 0) {
            $results = Countries::query();
        }

        $results = $results->get()
            ->map(fn($country) => [
                'id' => $country->id,
                'text' => $country->name,
                'country_code' => $country->iso_3166_2
            ]);

        return response()->json([
            'results' => $results
        ]);
    }
}
