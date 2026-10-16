<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->topLevel()
            ->withCount('children')
            ->orderBy('name')
            ->take(8)
            ->get();

        $featuredCourses = Course::query()
            ->where('status', 'active')
            ->with(['instructor', 'category'])
            ->latest()
            ->take(24)
            ->get();

        return view('welcome', compact('categories', 'featuredCourses'));
    }
}
