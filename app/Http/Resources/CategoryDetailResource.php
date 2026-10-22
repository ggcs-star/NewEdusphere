<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A single category plus the courses filed under it, built from the
 * array CategoryCatalogService::categoryDetail() returns.
 */
class CategoryDetailResource extends JsonResource
{
    public function __construct(private readonly array $detail)
    {
        parent::__construct($detail['category']);
    }

    public function toArray(Request $request): array
    {
        return array_merge(
            (new CategoryResource($this->resource))->resolve($request),
            [
                'courses' => CourseResource::collection($this->detail['courses']),
                'pagination' => [
                    'current_page' => $this->detail['courses']->currentPage(),
                    'last_page' => $this->detail['courses']->lastPage(),
                    'per_page' => $this->detail['courses']->perPage(),
                    'total' => $this->detail['courses']->total(),
                ],
            ]
        );
    }
}
