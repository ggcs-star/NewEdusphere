@php
    $coursePrice = method_exists($course, 'effectivePrice') ? $course->effectivePrice() : ($course->price ?? 0);
    $courseImage = $course->thumbnail ?? null;
    $courseUrl = url('/home/course/'.($course->slug ?? 'course').'/'.$course->id);
@endphp
<article class="course-card">
    @if($courseImage)
        <div class="course-thumb" style="padding:0"><img src="{{ $course->thumbnailUrl() }}" alt="{{ $course->title ?? 'Course' }}" style="width:100%;height:100%;object-fit:cover"></div>
    @else
        <div class="course-thumb" aria-hidden="true">🎓</div>
    @endif
    <div class="course-body">
        <div class="eyebrow">{{ $course->category->name ?? 'Featured course' }}</div>
        <h3><a href="{{ $courseUrl }}">{{ $course->title ?? 'Untitled course' }}</a></h3>
        <p class="muted">{{ \Illuminate\Support\Str::limit(strip_tags($course->short_description ?? $course->description ?? 'Build practical skills with this course.'), 105) }}</p>
        <div class="meta-row"><span>◷ {{ $course->duration ?? 'Self-paced' }}</span><span>★ {{ $course->rating ?? 'New' }}</span></div>
        <div class="list-row">
            <span class="price">{{ !empty($course->is_free) ? 'Free' : '₹'.number_format((float)$coursePrice, 0) }}</span>
            <a class="btn btn-purple" href="{{ $courseUrl }}">View course</a>
        </div>
        <form method="POST" action="{{ url('/cart/add/'.($course->id ?? 0)) }}" style="margin-top:10px">@csrf<button class="btn btn-light" type="submit" style="width:100%">Add to cart</button></form>
    </div>
</article>
