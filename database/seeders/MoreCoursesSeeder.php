<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MoreCoursesSeeder extends Seeder
{
    /**
     * Adds 4 more fully-populated courses (own category, instructor,
     * pricing, curriculum) and enrolls the existing demo students into
     * several of them, so a student's Enrollment Details page has
     * multiple "other courses" to show instead of just one.
     */
    public function run(): void
    {
        $anuj = User::where('email', 'anuj.singh@edusphere.test')->first();

        $meera = User::updateOrCreate(
            ['email' => 'meera.nair@edusphere.test'],
            [
                'name' => 'Meera Nair',
                'password' => Hash::make('Instructor@123'),
                'role' => 'user',
                'is_instructor' => true,
                'status' => true,
                'biography' => 'Data scientist and former ML engineer at a Series B startup, teaching practical, project-based data science to over 20,000 students.',
            ]
        );

        $rohan = User::updateOrCreate(
            ['email' => 'rohan.mehta@edusphere.test'],
            [
                'name' => 'Rohan Mehta',
                'password' => Hash::make('Instructor@123'),
                'role' => 'user',
                'is_instructor' => true,
                'status' => true,
                'biography' => 'Growth marketer who has run digital campaigns for over 100 brands, now teaching the strategies that actually move the needle.',
            ]
        );

        $priya = User::where('email', 'priya.sharma@edusphere.test')->first();
        $rahul = User::where('email', 'rahul.verma@edusphere.test')->first();

        $courses = [
            [
                'instructor' => $meera,
                'category' => ['name' => 'Data Science', 'icon' => 'fa-solid fa-chart-line'],
                'title' => 'Data Science & Machine Learning Masterclass',
                'short_description' => 'Learn Python, Pandas, NumPy, and build real machine learning models from scratch.',
                'outcomes' => [
                    'Clean and analyze real-world datasets with Pandas and NumPy',
                    'Build and evaluate machine learning models with scikit-learn',
                    'Visualize data with Matplotlib and Seaborn',
                    'Deploy a trained model behind a simple API',
                ],
                'requirements' => [
                    'Basic Python knowledge is helpful but not required',
                    'A computer with at least 4GB of RAM',
                ],
                'language' => 'English',
                'level' => 'intermediate',
                'price' => 1299,
                'discount_price' => 399,
                'is_free' => false,
                'preview_video_url' => 'https://www.youtube.com/watch?v=ua-CiDNNj30',
                'is_top_course' => true,
                'sections' => [
                    ['title' => 'Python for Data Science', 'lessons' => ['NumPy Arrays', 'Pandas DataFrames', 'Data Cleaning Techniques']],
                    ['title' => 'Machine Learning Foundations', 'lessons' => ['Supervised vs Unsupervised Learning', 'Training Your First Model', 'Module Quiz: ML Basics']],
                ],
                'enroll' => [$priya, $rahul],
            ],
            [
                'instructor' => $anuj,
                'category' => ['name' => 'Design', 'icon' => 'fa-solid fa-palette'],
                'title' => 'UI/UX Design Fundamentals',
                'short_description' => 'Design beautiful, usable interfaces with Figma — from wireframes to high-fidelity prototypes.',
                'outcomes' => [
                    'Understand core UX principles and user research methods',
                    'Create wireframes and interactive prototypes in Figma',
                    'Build a cohesive design system with reusable components',
                ],
                'requirements' => [
                    'No design experience required',
                    'A free Figma account',
                ],
                'language' => 'English',
                'level' => 'beginner',
                'price' => 799,
                'discount_price' => null,
                'is_free' => false,
                'preview_video_url' => 'https://www.youtube.com/watch?v=c9Wg6Cb_YlU',
                'is_top_course' => false,
                'sections' => [
                    ['title' => 'UX Foundations', 'lessons' => ['User Research Basics', 'Wireframing Principles']],
                    ['title' => 'Designing in Figma', 'lessons' => ['Components & Variants', 'Prototyping Interactions']],
                ],
                'enroll' => [$priya],
            ],
            [
                'instructor' => $rohan,
                'category' => ['name' => 'Business', 'icon' => 'fa-solid fa-briefcase'],
                'title' => 'Digital Marketing Bootcamp',
                'short_description' => 'A complete, free introduction to SEO, social media, and paid ads for growing any business online.',
                'outcomes' => [
                    'Plan and run a social media marketing campaign',
                    'Understand the fundamentals of SEO and content marketing',
                    'Set up and optimize a paid ad campaign',
                ],
                'requirements' => [
                    'No prior marketing experience needed',
                ],
                'language' => 'English',
                'level' => 'all_levels',
                'price' => 0,
                'discount_price' => null,
                'is_free' => true,
                'preview_video_url' => 'https://www.youtube.com/watch?v=nU-IIXBWlS4',
                'is_top_course' => false,
                'sections' => [
                    ['title' => 'Marketing Foundations', 'lessons' => ['Know Your Audience', 'Building a Content Strategy']],
                ],
                'enroll' => [$priya],
            ],
            [
                'instructor' => $anuj,
                'category' => ['name' => 'Mobile Development', 'icon' => 'fa-solid fa-mobile-screen'],
                'title' => 'Flutter & Dart: Complete Mobile App Development',
                'short_description' => 'Build native iOS and Android apps from a single codebase using Flutter and Dart.',
                'outcomes' => [
                    'Build responsive, cross-platform UIs with Flutter widgets',
                    'Manage app state with Provider and Riverpod',
                    'Connect your app to a REST API and Firebase',
                    'Publish your app to the App Store and Play Store',
                ],
                'requirements' => [
                    'Basic programming knowledge in any language',
                    'A Mac is only required for publishing to the App Store',
                ],
                'language' => 'English',
                'level' => 'advanced',
                'price' => 1499,
                'discount_price' => 599,
                'is_free' => false,
                'preview_video_url' => 'https://www.youtube.com/watch?v=1xipg02Wu8s',
                'is_top_course' => true,
                'sections' => [
                    ['title' => 'Flutter Fundamentals', 'lessons' => ['Widgets & Layouts', 'Navigation & Routing']],
                    ['title' => 'State Management & APIs', 'lessons' => ['Provider vs Riverpod', 'Calling a REST API', 'Module Quiz: State Management']],
                ],
                'enroll' => [$priya, $rahul],
            ],
        ];

        foreach ($courses as $i => $data) {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($data['category']['name'])],
                [
                    'name' => $data['category']['name'],
                    'code' => Str::upper(Str::random(8)),
                    'icon' => $data['category']['icon'],
                    'status' => true,
                ]
            );

            $course = Course::updateOrCreate(
                ['slug' => Str::slug($data['title'])],
                [
                    'user_id' => $data['instructor']->id,
                    'category_id' => $category->id,
                    'sub_category_id' => null,
                    'title' => $data['title'],
                    'short_description' => $data['short_description'],
                    'description' => '<p>'.$data['short_description'].'</p><h3>What you\'ll learn</h3><ul><li>'.implode('</li><li>', $data['outcomes']).'</li></ul>',
                    'outcomes' => $data['outcomes'],
                    'requirements' => $data['requirements'],
                    'language' => $data['language'],
                    'level' => $data['level'],
                    'price' => $data['price'],
                    'discount_price' => $data['discount_price'],
                    'is_free' => $data['is_free'],
                    'preview_video_type' => 'youtube',
                    'preview_video_url' => $data['preview_video_url'],
                    'meta_keywords' => Str::lower($data['category']['name']).', '.Str::slug($data['title'], ' '),
                    'meta_description' => $data['short_description'],
                    'is_top_course' => $data['is_top_course'],
                    'status' => 'active',
                ]
            );

            $course->sections()->delete();

            foreach ($data['sections'] as $sectionIndex => $sectionData) {
                $section = $course->sections()->create([
                    'title' => $sectionData['title'],
                    'order' => $sectionIndex,
                ]);

                foreach ($sectionData['lessons'] as $lessonIndex => $lessonTitle) {
                    $isQuiz = str_starts_with($lessonTitle, 'Module Quiz');

                    $lesson = $section->lessons()->create([
                        'course_id' => $course->id,
                        'title' => $lessonTitle,
                        'lesson_type' => $isQuiz ? 'quiz' : 'video',
                        'video_type' => $isQuiz ? null : 'youtube',
                        'video_url' => $isQuiz ? null : $data['preview_video_url'],
                        'duration' => $isQuiz ? null : sprintf('%02d:%02d', rand(8, 25), rand(10, 59)),
                        'order' => $lessonIndex,
                    ]);

                    if ($isQuiz) {
                        $lesson->questions()->create([
                            'title' => 'Which statement best describes this module\'s core concept?',
                            'type' => 'single_choice',
                            'options' => ['The correct, module-specific answer', 'An unrelated distractor', 'A partially correct answer', 'None of the above'],
                            'correct_answers' => [0],
                            'order' => 0,
                        ]);
                    }
                }
            }

            foreach ($data['enroll'] as $j => $student) {
                Enrollment::firstOrCreate(['user_id' => $student->id, 'course_id' => $course->id]);

                if (! $data['is_free']) {
                    $amount = $data['discount_price'] ?? $data['price'];

                    Payment::firstOrCreate(
                        ['user_id' => $student->id, 'course_id' => $course->id],
                        [
                            'payment_method' => ($i + $j) % 2 === 0 ? 'paypal' : 'stripe',
                            'amount' => $amount,
                            'instructor_revenue' => round($amount * 0.70, 2),
                            'admin_revenue' => round($amount * 0.30, 2),
                            'instructor_paid_out' => false,
                        ]
                    );
                }
            }
        }
    }
}
