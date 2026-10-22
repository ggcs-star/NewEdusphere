<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\CategoryCatalogService;
use App\Services\CourseCatalogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function __construct(
        private readonly CourseCatalogService $catalog,
        private readonly CategoryCatalogService $categories,
    ) {
    }

    /**
     * Display all active courses.
     */
    public function index(Request $request): View
    {
        return view('frontend.courses.index', [
            'courses' => $this->catalog->listCourses($request->only('q', 'category', 'price')),
            'categories' => $this->catalog->activeCategories(),
        ]);
    }

    /**
     * Display course details with free-preview and purchase status.
     *
     * First two lessons across the entire course are free previews.
     * Remaining lessons require course access.
     */
    public function show(string $slug, int $id): View
    {
        $viewer = function_exists('current_admin_user')
            ? current_admin_user()
            : null;

        $detail = $this->catalog->courseDetail($slug, $id, $viewer);

        return view('frontend.courses.show', [
            'course' => $detail['course'],
            'relatedCourses' => $detail['relatedCourses'],
            'isEnrolled' => $detail['isEnrolled'],
            'isCourseUnlocked' => $detail['isCourseUnlocked'],
            'previewLessonIds' => $detail['previewLessonIds'],
            'quizScoreSummary' => $detail['quizScoreSummary'],
        ]);
    }

    /**
     * Display a lesson only if it is free preview or the viewer
     * has access to the course.
     */
    public function learn(string $slug, int $lesson): View
    {
        $viewer = function_exists('current_admin_user')
            ? current_admin_user()
            : null;

        $access = $this->catalog->lessonAccess($slug, $lesson, $viewer);

        return view('frontend.courses.learn', [
            'course' => $access['course'],
            'currentLesson' => $access['lesson'],
            'isEnrolled' => $access['isEnrolled'],
            'isPreviewLesson' => $access['isPreviewLesson'],
        ]);
    }

    /**
     * Display course categories.
     */
    public function categories(): View
    {
        return view('frontend.categories.index', [
            'categories' => $this->categories->listCategories(perPage: 18),
        ]);
    }
}
