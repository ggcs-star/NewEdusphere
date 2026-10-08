<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    /**
     * Seeds one fully-populated course — instructor, category, pricing,
     * outcomes/requirements, a full section/lesson/quiz curriculum,
     * enrollments and payments — so every admin screen (Courses,
     * Enrollment History, Revenue) has real data to show, matching the
     * fields found on the reference LMS course detail page.
     */
    public function run(): void
    {
        $instructor = User::updateOrCreate(
            ['email' => 'anuj.singh@edusphere.test'],
            [
                'name' => 'Anuj Singh',
                'password' => 'Instructor@123',
                'role' => 'user',
                'is_instructor' => true,
                'status' => 'active',
                'biography' => 'Full-stack web developer and instructor with over 8 years of experience building production applications and teaching more than 50,000 students worldwide. Passionate about breaking down complex topics into clear, practical lessons.',
            ]
        );

        $studentOne = User::updateOrCreate(
            ['email' => 'priya.sharma@edusphere.test'],
            ['name' => 'Priya Sharma', 'password' => 'Student@123', 'role' => 'user', 'status' => 'active']
        );

        $studentTwo = User::updateOrCreate(
            ['email' => 'rahul.verma@edusphere.test'],
            ['name' => 'Rahul Verma', 'password' => 'Student@123', 'role' => 'user', 'status' => 'active']
        );

        $category = Category::firstOrCreate(
            ['slug' => 'web-development'],
            ['name' => 'Web Development', 'code' => Str::upper(Str::random(8)), 'icon' => 'fa-solid fa-code', 'status' => true]
        );

        $subCategory = Category::firstOrCreate(
            ['slug' => 'web-development-frontend'],
            ['name' => 'Front-End Development', 'parent_id' => $category->id, 'code' => Str::upper(Str::random(8)), 'status' => true]
        );

        $course = Course::updateOrCreate(
            ['slug' => 'the-complete-2024-web-development-bootcamp'],
            [
                'user_id' => $instructor->id,
                'category_id' => $category->id,
                'sub_category_id' => $subCategory->id,
                'title' => 'The Complete 2024 Web Development Bootcamp',
                'short_description' => 'Become a full-stack web developer with just one course. HTML, CSS, JavaScript, Node, React, MongoDB and more.',
                'description' => $this->description(),
                'outcomes' => [
                    'Build 16+ web development projects for your portfolio, ready to apply for junior developer jobs',
                    'Master backend development with Node, Express, MongoDB, SQL and web3/blockchain development',
                    'Learn the latest technologies, including JavaScript, React, and Web3 development',
                    'Build fully-fledged websites and web apps for your startup or business',
                    'Master frontend development with React',
                    'Learn professional developer best practices, including Flexbox and CSS Grid',
                ],
                'requirements' => [
                    'No programming experience needed — I will teach you everything you need to know',
                    'A computer with access to the internet',
                    'No paid software required',
                    'I will walk you through, step-by-step, how to install all the free software you need',
                ],
                'language' => 'English',
                'level' => 'beginner',
                'price' => 999,
                'discount_price' => 199,
                'is_free' => false,
                'preview_video_type' => 'youtube',
                'preview_video_url' => 'https://www.youtube.com/watch?v=zJSY8tbf_ys',
                'meta_keywords' => 'web development, html, css, javascript, node js, react, full stack bootcamp',
                'meta_description' => 'Become a full-stack web developer with one course. HTML, CSS, Javascript, React, Node, MongoDB and more.',
                'is_top_course' => true,
                'status' => 'active',
            ]
        );

        // Reset any sections from a previous seeder run so this stays idempotent.
        $course->sections()->delete();

        $this->buildCurriculum($course);

        foreach ([$studentOne, $studentTwo] as $i => $student) {
            Enrollment::firstOrCreate(['user_id' => $student->id, 'course_id' => $course->id]);

            Payment::firstOrCreate(
                ['user_id' => $student->id, 'course_id' => $course->id],
                [
                    'payment_method' => $i === 0 ? 'paypal' : 'stripe',
                    'amount' => $course->discount_price,
                    'instructor_revenue' => round($course->discount_price * 0.70, 2),
                    'admin_revenue' => round($course->discount_price * 0.30, 2),
                    'instructor_paid_out' => false,
                ]
            );
        }
    }

    private function buildCurriculum(Course $course): void
    {
        $curriculum = [
            [
                'title' => 'Introduction',
                'lessons' => [
                    ['title' => 'Course Overview', 'type' => 'document', 'duration' => '03:40'],
                    ['title' => 'How to Get the Most Out of This Course', 'type' => 'video', 'duration' => '05:12'],
                ],
            ],
            [
                'title' => 'Front-End Web Development',
                'lessons' => [
                    ['title' => 'Introduction to HTML', 'type' => 'video', 'duration' => '14:22'],
                    ['title' => 'HTML Elements & Page Structure', 'type' => 'video', 'duration' => '18:05'],
                    ['title' => 'Introduction to CSS', 'type' => 'video', 'duration' => '16:48'],
                    ['title' => 'CSS Flexbox & Grid', 'type' => 'video', 'duration' => '22:10'],
                    ['title' => 'JavaScript Fundamentals', 'type' => 'video', 'duration' => '28:35'],
                ],
            ],
            [
                'title' => 'Multi-Page Website Project',
                'lessons' => [
                    ['title' => 'Planning the Project', 'type' => 'document', 'duration' => '06:00'],
                    ['title' => 'Building the Homepage', 'type' => 'video', 'duration' => '24:18'],
                    ['title' => 'Making the Layout Responsive', 'type' => 'video', 'duration' => '19:52'],
                ],
            ],
            [
                'title' => 'Node.js & Backend Development',
                'lessons' => [
                    ['title' => 'Introduction to Node.js', 'type' => 'video', 'duration' => '17:40'],
                    ['title' => 'Building REST APIs with Express', 'type' => 'video', 'duration' => '26:15'],
                    ['title' => 'Connecting to MongoDB', 'type' => 'video', 'duration' => '21:03'],
                    ['title' => 'Module Quiz: Backend Basics', 'type' => 'quiz'],
                ],
            ],
        ];

        foreach ($curriculum as $sectionIndex => $sectionData) {
            $section = $course->sections()->create([
                'title' => $sectionData['title'],
                'order' => $sectionIndex,
            ]);

            foreach ($sectionData['lessons'] as $lessonIndex => $lessonData) {
                $lesson = $section->lessons()->create([
                    'course_id' => $course->id,
                    'title' => $lessonData['title'],
                    'lesson_type' => $lessonData['type'],
                    'video_type' => $lessonData['type'] === 'video' ? 'youtube' : null,
                    'video_url' => $lessonData['type'] === 'video' ? 'https://www.youtube.com/watch?v=zJSY8tbf_ys' : null,
                    'duration' => $lessonData['duration'] ?? null,
                    'order' => $lessonIndex,
                ]);

                if ($lessonData['type'] === 'quiz') {
                    $this->buildQuizQuestions($lesson);
                }
            }
        }
    }

    private function buildQuizQuestions(Lesson $lesson): void
    {
        $questions = [
            [
                'title' => 'What command initializes a new Node.js project?',
                'type' => 'single_choice',
                'options' => ['npm init', 'node start', 'npm build', 'node create'],
                'correct_answers' => [0],
            ],
            [
                'title' => 'Which of the following are valid Express middleware functions?',
                'type' => 'multiple_choice',
                'options' => ['express.json()', 'express.urlencoded()', 'express.static()', 'express.database()'],
                'correct_answers' => [0, 1, 2],
            ],
            [
                'title' => 'MongoDB stores data in which format?',
                'type' => 'single_choice',
                'options' => ['BSON/JSON-like documents', 'SQL tables', 'XML files', 'CSV rows'],
                'correct_answers' => [0],
            ],
        ];

        foreach ($questions as $i => $q) {
            $lesson->questions()->create([
                'title' => $q['title'],
                'type' => $q['type'],
                'options' => $q['options'],
                'correct_answers' => $q['correct_answers'],
                'order' => $i,
            ]);
        }
    }

    private function description(): string
    {
        return <<<'HTML'
            <p>Welcome to the <strong>Complete 2024 Web Development Bootcamp</strong> — the only course you need to become a full-stack web developer. With over 150,000 ratings and a 4.8 average, this is one of the most popular programming courses on the internet.</p>
            <p>This course includes over 60 hours of content with animated explanation videos and real-world projects. It has been developed over four years, with input from thousands of students just like you.</p>
            <h3>By the end of this course, you'll be able to:</h3>
            <ul>
                <li>Build fully responsive websites from scratch using HTML and CSS</li>
                <li>Write modern, maintainable JavaScript to power interactive web apps</li>
                <li>Build backend REST APIs with Node.js, Express and MongoDB</li>
                <li>Deploy full-stack applications to the web</li>
            </ul>
            <p>Whether you're a complete beginner or already have some experience, this course will take you from zero to job-ready.</p>
            HTML;
    }
}
