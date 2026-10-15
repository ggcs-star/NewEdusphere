@props(['category'])
@if ($category->thumbnail)
    <img
        src="{{ $category->thumbnailUrl() }}"
        alt="{{ $category->name }}"
        loading="lazy"
    >
@elseif ($category->icon && \Illuminate\Support\Str::contains($category->icon, ['fa-', 'bi-']))
    <i class="{{ $category->icon }}" aria-hidden="true"></i>
@elseif ($category->icon)
    <span>{{ $category->icon }}</span>
@else
    <x-icon name="cap" />
@endif
