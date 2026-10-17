<section class="hero">
    <div class="hero-decoration"></div>

    <div class="container hero-grid">
        <div class="hero-content">
            <div class="hero-kicker">Learn Anytime, Anywhere</div>

            @if ($siteSettings['banner_title'] === \App\Models\Setting::DEFAULTS['frontend']['banner_title'])
                <h1>Build Your Future With <span>EduSphere</span></h1>
            @else
                <h1>{{ $siteSettings['banner_title'] }}</h1>
            @endif

            <p class="hero-description">
                {{ $siteSettings['banner_sub_title'] }}
            </p>

            <div class="hero-buttons">
                <a href="#courses" class="btn btn-gold">
                    Explore Courses <x-icon name="arrow" />
                </a>
                <a href="#" class="btn btn-outline-light">
                    <svg viewBox="0 0 24 24" aria-hidden="true" style="width:1em;height:1em;margin-right:8px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;vertical-align:-2px;"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
                    Watch Video
                </a>
            </div>

            <div class="hero-feature-row">
                <div class="feature-item"><span class="feature-icon"><x-icon name="book" /></span> Expert Instructors</div>
                <div class="feature-item"><span class="feature-icon"><x-icon name="shield" /></span> Lifetime Access</div>
                <div class="feature-item"><span class="feature-icon"><x-icon name="group" /></span> Learn at Your Pace</div>
            </div>
        </div>

        <div class="hero-image">
          <div class="hero-visual-card">
            <img
                src="{{ $siteSettings['banner_image'] ? Storage::url($siteSettings['banner_image']) : asset('images/background.png') }}"
                alt="Student learning online with {{ $siteSettings['system_name'] }} — video lectures, quizzes and certificates"
                class="hero-visual"
                fetchpriority="high"
            >
          </div>
        </div>
    </div>
</section>
