<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Shared by any controller that needs a URL-safe, collision-free slug
 * (Category, Course). Appends -1, -2, ... until the slug is free.
 */
trait GeneratesUniqueSlugs
{
    protected function uniqueSlug(string $modelClass, string $name, ?int $ignoreId = null, string $column = 'slug'): string
    {
        /** @var class-string<Model> $modelClass */
        $slug = Str::slug($name);
        $original = $slug;
        $counter = 1;

        while (
            $modelClass::where($column, $slug)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $slug = "{$original}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
