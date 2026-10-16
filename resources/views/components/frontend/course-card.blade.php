@php
    $coursePrice = method_exists($course, 'effectivePrice')
        ? $course->effectivePrice()
        : ($course->price ?? 0);

    $courseImage = $course->thumbnail ?? null;

    $courseUrl = url(
        '/home/course/' .
        ($course->slug ?? 'course') .
        '/' .
        $course->id
    );
@endphp
<article class="course-card">

    {{-- Course image: always separate from the text --}}
    <a href="{{ $courseUrl }}" class="course-thumb">
        @if($courseImage)
            <img
                src="{{ $course->thumbnailUrl() }}"
                alt="{{ $course->title ?? 'Course' }}"
                loading="lazy"
            >
        @else
            <span class="course-image-placeholder" aria-hidden="true"></span>
        @endif
    </a>

    <div class="course-body">

        <div class="eyebrow">
            {{ $course->category->name ?? 'Featured course' }}
        </div>

        <h3>
            <a href="{{ $courseUrl }}">
                {{ $course->title ?? 'Untitled course' }}
            </a>
        </h3>

        <p class="muted">
            {{ \Illuminate\Support\Str::limit(
                strip_tags($course->short_description ?? $course->description ?? 'Build practical skills with this course.'),
                105
            ) }}
        </p>

        <div class="meta-row">
            <span>◷ {{ $course->duration ?? 'Self-paced' }}</span>
            <span>★ {{ $course->rating ?? 'New' }}</span>
        </div>

        <div class="list-row">
            <span class="price">
                {{ !empty($course->is_free) ? 'Free' : '₹' . number_format((float) $coursePrice, 0) }}
            </span>

            <a class="btn btn-purple" href="{{ $courseUrl }}">
                View course
            </a>
        </div>

        <form
            method="POST"
            action="{{ url('/cart/add/' . ($course->id ?? 0)) }}"
            class="course-cart-form"
        >
            @csrf

            <button class="btn btn-light" type="submit">
                Add to cart
            </button>
        </form>

    </div>
</article>
