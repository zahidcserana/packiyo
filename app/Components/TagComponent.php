<?php
namespace App\Components;

use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagComponent extends BaseComponent {

    public function filterInputTags(Request $request): JsonResponse
    {
        $term = $request->get('term');
        $results = [];
        $customerIds = app('user')->getSelectedCustomers()->pluck('id')->toArray();

        $tags = [];

        if ($term) {
            $term = $term . '%';

            $tags = Tag::where('name', 'like', $term)
                ->whereIn('customer_id', $customerIds)
                ->get();
        }

        foreach ($tags as $tag) {
            $results[] = [
                'id' => $tag->name,
                'text' => $tag->name
            ];
        }

        return response()->json([
            'results' => $results
        ]);
    }
}
