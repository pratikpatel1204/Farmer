<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\SubCategory;
use Illuminate\Http\Request;

/**
 * Sub categories (admin ke Sub Category Master se). Product add / edit form ke dropdown ke liye.
 */
class SubCategoryController extends ApiController
{
    /**
     * GET /sub-categories               → { "12": [{id, name}], ... }  (category_id ke hisaab se group)
     * GET /sub-categories?category_id=12 → [{id, name}, ...]
     */
    public function index(Request $request)
    {
        $query = SubCategory::orderBy('name');

        if ($request->filled('category_id')) {
            return $this->ok(
                $query->where('category_id', (int) $request->query('category_id'))
                    ->get(['id', 'name'])
                    ->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])
                    ->values()
            );
        }

        $grouped = $query->get(['id', 'category_id', 'name'])
            ->groupBy('category_id')
            ->map(fn ($items) => $items->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values());

        // Khali ho to bhi JSON object ({}), array ([]) nahi
        return $this->ok($grouped->isEmpty() ? (object) [] : $grouped);
    }
}
