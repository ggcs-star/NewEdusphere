@props(['paginator'])

<div class="mt-5 flex flex-wrap items-center justify-between gap-3 px-1">
    <p class="text-sm text-slate-500">
        @if ($paginator->total() > 0)
            Showing <span class="font-semibold text-slate-700">{{ $paginator->firstItem() }}</span>
            to <span class="font-semibold text-slate-700">{{ $paginator->lastItem() }}</span>
            of <span class="font-semibold text-slate-700">{{ $paginator->total() }}</span> results
        @else
            No results
        @endif
    </p>

    <div class="flex items-center gap-1.5">
        @if ($paginator->onFirstPage())
            <span class="h-9 w-9 rounded-lg bg-slate-100 text-slate-300 flex items-center justify-center">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="h-9 w-9 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-brand-50 hover:text-brand-700 flex items-center justify-center transition">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </a>
        @endif

        @foreach ($paginator->getUrlRange(1, max($paginator->lastPage(), 1)) as $page => $url)
            @if ($page === $paginator->currentPage())
                <span class="h-9 w-9 rounded-lg bg-brand-900 text-white text-sm font-semibold flex items-center justify-center">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="h-9 w-9 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-brand-50 hover:text-brand-700 text-sm font-medium flex items-center justify-center transition">{{ $page }}</a>
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="h-9 w-9 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-brand-50 hover:text-brand-700 flex items-center justify-center transition">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </a>
        @else
            <span class="h-9 w-9 rounded-lg bg-slate-100 text-slate-300 flex items-center justify-center">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </span>
        @endif
    </div>
</div>
