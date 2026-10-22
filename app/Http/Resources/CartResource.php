<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Built from CartService::items()/subtotal() — a plain
 * [['course' => Course, 'price' => float], ...] collection plus total.
 */
class CartResource extends JsonResource
{
    public function __construct(private readonly \Illuminate\Support\Collection $items, private readonly float $subtotal)
    {
        parent::__construct($items);
    }

    public function toArray(Request $request): array
    {
        return [
            'items' => $this->items->map(fn (array $item) => [
                'course' => new CourseResource($item['course']),
                'price' => $item['price'],
            ])->values()->all(),
            'subtotal' => $this->subtotal,
            'count' => $this->items->count(),
        ];
    }
}
