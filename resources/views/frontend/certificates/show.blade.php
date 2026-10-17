@extends('layouts.frontend')

@section('title', 'Course Certificate')

@section('content')
<div class="certificate-page">
    <div class="certificate-actions">
        <a href="{{ route('user.certificates.index') }}" class="back-link">
            ← My Certificates
        </a>

        <button type="button" onclick="window.print()" class="print-btn">
            🖨 Print / Save as PDF
        </button>
    </div>

    @php
        $data = $certificate->data ?? [];
        $studentName = $data['student_name'] ?? $certificate->user->name ?? 'Student';
        $courseName = $data['course_name'] ?? $certificate->course->title ?? 'Course';
        $completionDate = $data['completion_date'] ?? optional($certificate->issued_at)->format('Y-m-d');
        $certificateNumber = $data['certificate_number'] ?? $certificate->certificate_number;
        $template = $certificate->template;
        $background = $template?->background_image;
    @endphp

    <div class="certificate-sheet">
        @if($background)
            <div class="certificate-background"
                 style="background-image: url('{{ asset('storage/' . ltrim($background, '/')) }}');">
            </div>
        @endif

        <div class="certificate-content">
            <div class="certificate-brand">EDUSPHERE</div>
            <div class="certificate-label">CERTIFICATE OF COMPLETION</div>

            <div class="decorative-line"></div>

            <p class="presented">This certificate is proudly presented to</p>

            <h1 class="student-name">{{ $studentName }}</h1>

            <p class="certificate-description">
                for successfully completing the course
            </p>

            <h2 class="course-name">{{ $courseName }}</h2>

            <p class="certificate-description">
                and fulfilling the course completion requirements.
            </p>

            <div class="certificate-footer">
                <div class="signature-block">
                    <div class="signature-line"></div>
                    <strong>EduSphere</strong>
                    <span>Learning Platform</span>
                </div>

                <div class="date-block">
                    <strong>
                        {{ $completionDate ? \Illuminate\Support\Carbon::parse($completionDate)->format('d M Y') : 'N/A' }}
                    </strong>
                    <span>Date of Completion</span>
                </div>
            </div>

            <div class="certificate-number">
                Certificate ID: {{ $certificateNumber }}
            </div>
        </div>
    </div>
</div>

<style>
.certificate-page {
    max-width: 1200px;
    margin: 35px auto;
    padding: 0 20px;
}
.certificate-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-bottom: 22px;
    flex-wrap: wrap;
}
.back-link {
    color: #51277d;
    text-decoration: none;
    font-weight: 700;
}
.print-btn {
    border: 0;
    border-radius: 8px;
    padding: 12px 18px;
    background: #51277d;
    color: #fff;
    font-weight: 700;
    cursor: pointer;
}
.certificate-sheet {
    position: relative;
    overflow: hidden;
    min-height: 650px;
    padding: 18px;
    background: #fff;
    border: 3px solid #b99552;
    box-shadow: 0 10px 35px rgba(0,0,0,.09);
}
.certificate-sheet::before {
    content: "";
    position: absolute;
    inset: 10px;
    border: 1px solid #b99552;
    pointer-events: none;
    z-index: 2;
}
.certificate-background {
    position: absolute;
    inset: 0;
    background-position: center;
    background-size: cover;
    opacity: .15;
}
.certificate-content {
    position: relative;
    z-index: 3;
    min-height: 600px;
    padding: 55px 35px 30px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.certificate-brand {
    color: #51277d;
    font-size: 21px;
    font-weight: 900;
    letter-spacing: 5px;
}
.certificate-label {
    color: #9b793b;
    font-size: clamp(22px, 4vw, 38px);
    font-weight: 800;
    margin-top: 25px;
    letter-spacing: 2px;
}
.decorative-line {
    width: 130px;
    height: 3px;
    background: #b99552;
    margin: 22px auto;
}
.presented, .certificate-description {
    color: #625b6c;
    font-size: 15px;
    line-height: 1.7;
}
.student-name {
    color: #281743;
    font-size: clamp(30px, 5vw, 48px);
    font-family: Georgia, serif;
    font-weight: 700;
    overflow-wrap: anywhere;
    margin: 15px 0;
}
.course-name {
    color: #51277d;
    font-size: clamp(21px, 3vw, 30px);
    font-weight: 800;
    margin: 12px 0;
    overflow-wrap: anywhere;
}
.certificate-footer {
    display: flex;
    justify-content: space-around;
    align-items: flex-end;
    gap: 45px;
    width: 100%;
    margin-top: 45px;
}
.signature-block, .date-block {
    display: flex;
    flex-direction: column;
    min-width: 150px;
    gap: 5px;
    color: #281743;
}
.signature-block span, .date-block span {
    color: #777;
    font-size: 12px;
}
.signature-line {
    border-top: 1px solid #a8a0b0;
    width: 180px;
    max-width: 100%;
    margin: 0 auto 8px;
}
.certificate-number {
    margin-top: 40px;
    color: #777;
    font-size: 11px;
    overflow-wrap: anywhere;
}
@media (max-width: 600px) {
    .certificate-content {
        padding: 40px 18px 25px;
    }
    .certificate-footer {
        gap: 25px;
        flex-wrap: wrap;
        margin-top: 25px;
    }
    .certificate-sheet {
        min-height: 600px;
        padding: 12px;
    }
}
@media print {
    @page {
        size: A4 landscape;
        margin: 8mm;
    }
    body * {
        visibility: hidden !important;
    }
    .certificate-sheet, .certificate-sheet * {
        visibility: visible !important;
    }
    .certificate-sheet {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        min-height: 180mm;
        box-sizing: border-box;
        box-shadow: none;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .certificate-content {
        min-height: 165mm;
    }
    .certificate-actions {
        display: none !important;
    }
}
</style>
@endsection
