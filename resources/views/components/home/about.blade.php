<section class="section" id="about">
    <div class="container about-grid">
        <div class="about-image">
            <img
                src="https://images.unsplash.com/photo-1523580846011-d3a5bc25702b?auto=format&fit=crop&w=1100&q=85"
                alt="Students learning together"
                loading="lazy"
            >
        </div>

        <div class="about-content">
            <div class="section-kicker">ABOUT EDUSPHERE</div>
            <h2>Learn Today.<br>Grow Every Day.</h2>

            <div class="prose">{!! $siteSettings['about_us'] !!}</div>

            <div class="about-points">
                <div class="about-point"><span class="check"><x-icon name="check" /></span> Flexible learning</div>
                <div class="about-point"><span class="check"><x-icon name="check" /></span> Multiple subjects</div>
                <div class="about-point"><span class="check"><x-icon name="check" /></span> Clear course details</div>
                <div class="about-point"><span class="check"><x-icon name="check" /></span> Learn at your pace</div>
            </div>

            <a href="{{ route('register') }}" class="btn btn-gold">
                START LEARNING <x-icon name="arrow" />
            </a>
        </div>
    </div>
</section>
