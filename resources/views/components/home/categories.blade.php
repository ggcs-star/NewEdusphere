@props(['categories'])
<section class="section categories-section" id="categories">
    <span class="dot-grid top-left" aria-hidden="true"></span>
    <span class="dot-grid bottom-right" aria-hidden="true"></span>
    <span class="bg-blob one" aria-hidden="true"></span>
    <span class="bg-blob two" aria-hidden="true"></span>

    <div class="container">
        <div class="section-heading">
            <div class="section-kicker">Discover Your Interests</div>
            <h2 class="section-title">Explore Categories</h2>
            <div class="section-line"></div>
            <p class="section-description">
                Find subjects that interest you and discover your next
                learning opportunity.
            </p>
        </div>

        <div class="category-grid">
            @forelse ($categories->sortByDesc(fn ($c) => (int) (bool) $c->thumbnail)->take(4) as $category)
                <a href="#courses" class="category-card">
                    <div class="category-icon">
                        <x-category-icon :category="$category" />
                    </div>

                    <h3>{{ $category->name }}</h3>
                    <p>{{ $category->children_count }} subcategories</p>
                </a>
            @empty
                <div class="categories-empty">
                    <h3>No Categories Yet</h3>
                    <p>Categories will appear when added by the administrator.</p>
                </div>
            @endforelse
        </div>

        @if ($categories->count() > 4)
            <div class="categories-cta">
                <a href="{{ route('categories.index') }}" class="btn btn-outline-light">
                    Show All Categories <x-icon name="arrow" />
                </a>
            </div>
        @endif
    </div>
</section>
