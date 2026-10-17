@extends('layouts.frontend')

@section('title', 'My Certificates')

@section('content')
<div class="container py-5">
    <div class="certificate-header mb-4">
        <div>
            <h1>My Certificates</h1>
            <p>View and download your course completion certificates.</p>
        </div>
        <a href="{{ route('my-courses.index') }}" class="back-btn">
            ← My Learning
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($certificates->count())
        <div class="certificate-grid">
            @foreach($certificates as $certificate)
                <div class="certificate-card">
                    <div class="certificate-icon">🏆</div>

                    <div class="certificate-info">
                        <h3>{{ $certificate->course->title ?? 'Course Certificate' }}</h3>

                        <p>
                            Certificate No:
                            <strong>{{ $certificate->certificate_number }}</strong>
                        </p>

                        <p>
                            Issued:
                            {{ $certificate->issued_at?->format('d M Y') ?? 'N/A' }}
                        </p>
                    </div>

                    <a
                        href="{{ route('user.certificates.show', $certificate->id) }}"
                        class="certificate-btn"
                    >
                        View Certificate →
                    </a>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $certificates->links() }}
        </div>
    @else
        <div class="empty-certificates">
            <div class="empty-icon">🎓</div>
            <h3>No certificates yet</h3>
            <p>
                Complete all lessons and quizzes in an enrolled course
                to receive your certificate.
            </p>
            <a href="{{ route('my-courses.index') }}" class="certificate-btn">
                Continue Learning
            </a>
        </div>
    @endif
</div>

<style>
.certificate-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}
.certificate-header h1 {
    font-size: 30px;
    font-weight: 800;
    margin-bottom: 6px;
    color: #211438;
}
.certificate-header p {
    color: #777;
    margin: 0;
}
.back-btn {
    color: #542a82;
    text-decoration: none;
    font-weight: 700;
}
.certificate-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
    gap: 20px;
}
.certificate-card {
    border: 1px solid #e9e1f2;
    border-radius: 15px;
    padding: 24px;
    background: #fff;
    box-shadow: 0 5px 20px rgba(40, 20, 65, .05);
}
.certificate-icon {
    font-size: 35px;
    margin-bottom: 14px;
}
.certificate-info h3 {
    font-size: 19px;
    font-weight: 800;
    color: #281743;
    margin-bottom: 15px;
}
.certificate-info p {
    font-size: 13px;
    color: #6b6574;
    overflow-wrap: anywhere;
}
.certificate-btn {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    padding: 11px 16px;
    background: #51277d;
    color: #fff !important;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 700;
    font-size: 13px;
    margin-top: 10px;
}
.certificate-btn:hover {
    background: #32105f;
}
.empty-certificates {
    padding: 55px 20px;
    text-align: center;
    border: 1px dashed #d9cce8;
    border-radius: 15px;
    background: #faf8fd;
}
.empty-icon {
    font-size: 48px;
    margin-bottom: 12px;
}
.empty-certificates h3 {
    font-weight: 800;
    color: #281743;
}
.empty-certificates p {
    color: #777;
    margin: 10px auto 18px;
    max-width: 500px;
}
</style>
@endsection