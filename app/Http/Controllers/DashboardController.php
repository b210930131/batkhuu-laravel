<?php

namespace App\Http\Controllers;

use App\Models\DashboardPost;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard');
    }

    public function customer(): View
    {
        $posts = DashboardPost::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->latest()
            ->get();

        return view('imagegen.customer.pages.dashboard', compact('posts'));
    }

    public function admin(): View
    {
        $postCount = DashboardPost::count();
        $publishedPostCount = DashboardPost::where('is_published', true)->count();

        return view('imagegen.admin.pages.dashboard', compact('postCount', 'publishedPostCount'));
    }
}
