<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Policy;

class PolicyController extends Controller
{
    // URL me jo naam aaye => policies.type ki value
   private array $types = [
    'privacy-policy'       => 'privacy',
    'privacy'              => 'privacy',
    'terms-and-conditions' => 'terms',
    'terms'                => 'terms',
];

private array $titles = [
    'privacy' => 'Privacy Policy',
    'terms'   => 'Terms & Conditions',
];

    // GET /api/policies
    public function index()
    {
        $data = Policy::whereIn('type', array_keys($this->titles))->get()
            ->map(fn ($policy) => $this->format($policy))->values();

        return response()->json(['success' => true, 'message' => 'OK', 'data' => $data]);
    }

    // GET /api/policies/{type}   e.g. privacy-policy, terms-and-conditions
    public function show(string $type)
    {
        $policy = $this->find($type);

        if (! $policy) {
            return response()->json(['success' => false, 'message' => 'Policy not found.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'OK', 'data' => $this->format($policy)]);
    }

    private function find(string $type): ?Policy
    {
        $type = $this->types[strtolower($type)] ?? strtolower($type);

        return Policy::where('type', $type)->latest('id')->first();
    }

    private function format(Policy $policy): array
    {
        return [
            'id'         => $policy->id,
            'type'       => $policy->type,
            'title'      => $this->titles[$policy->type] ?? ucwords(str_replace('_', ' ', $policy->type)),
            'content' => html_entity_decode((string) $policy->content, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'updated_at' => optional($policy->updated_at)->toIso8601String(),
        ];
    }
}