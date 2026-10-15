@props(['courses'])
@php
    $fallbackImages = [
        'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=900&q=80',
        'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=900&q=80',
        'https://images.unsplash.com/photo-1521737711867-e3b97375f902?auto=format&fit=crop&w=900&q=80',
    ];
@endphp
<section class="section featured-section" id="courses">
    <div class="container">
        <div class="section-heading">
            <div class="section-kicker">Find a Course</div>
            <h2 class="section-title">Featured Courses</h2>
            <div class="section-line"></div>
            <p class="section-description">
                Explore courses, discover new interests and develop skills
                that help you reach your goals.
            </p>
        </div>

        <div class="course-carousel" data-course-carousel>
            <div class="course-viewport">
                <div class="course-track" data-course-track>
            @forelse ($courses as $course)
                <article class="course-card">
                    <div class="course-image">
                        @if ($course->thumbnail)
                            <img
                                src="{{ $course->thumbnailUrl() }}"
                                alt="{{ $course->title }}"
                                loading="lazy"
                            >
                        @else
                            <img
                                src="{{ $fallbackImages[$loop->index % 3] }}"
                                alt="{{ $course->title }}"
                                loading="lazy"
                            >
                        @endif

                        @if ($course->is_top_course)
                            <span class="course-label"><x-icon name="star" /> Featured</span>
                        @elseif ($course->is_free)
                            <span class="course-label">Free Course</span>
                        @endif
                    </div>

                    <div class="course-content">
                        <div class="course-category">
                            {{ $course->category?->name ?? 'General Course' }}
                        </div>

                        <h3 class="course-title">{{ $course->title }}</h3>

                        <p class="course-description">
                            {{ \Illuminate\Support\Str::limit(
                                $course->short_description ?: ($course->description ?: 'Explore this course and develop useful skills through structured learning.'),
                                125
                            ) }}
                        </p>

                        <div class="course-meta">
                            <span><x-icon name="user" /> {{ $course->instructor?->name ?? 'EduSphere Team' }}</span>
                            <span><x-icon name="level" /> {{ ucfirst($course->level ?? 'All Levels') }}</span>
                        </div>

                        <div class="course-actions">
                            <div class="course-price">
                                @if ($course->is_free)
                                    Free
                                @else
                                    <span>₹{{ number_format($course->effectivePrice(), 0) }}</span>

                                    @if ($course->hasDiscount())
                                        <span class="course-old-price">
                                            ₹{{ number_format((float) $course->price, 0) }}
                                        </span>
                                    @endif
                                @endif
                            </div>

                            <a href="{{ route('register') }}" class="btn btn-gold">
                                MORE <x-icon name="arrow" />
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="empty-state">
                    <h3>Courses Coming Soon</h3>
                    <p>Active courses will appear here once published by the administrator.</p>
                </div>
            @endforelse
                </div>
            </div>

            @if ($courses->count() > 3)
                <button type="button" class="carousel-arrow prev" data-carousel-prev aria-label="Previous courses">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><polyline points="15 18 9 12 15 6" /></svg>
                </button>
                <button type="button" class="carousel-arrow next" data-carousel-next aria-label="Next courses">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><polyline points="9 18 15 12 9 6" /></svg>
                </button>
            @endif
        </div>

        @if ($courses->count() > 0)
            <div class="featured-cta">
                <a href="{{ route('courses.index') }}" class="btn btn-gold">EXPLORE COURSES <x-icon name="arrow" /></a>
            </div>
        @endif
    </div>
</section>
