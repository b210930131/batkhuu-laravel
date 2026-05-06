<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DashboardPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class DashboardPostController extends Controller
{
    public function index(): View
    {
        $posts = DashboardPost::query()
            ->orderBy('sort_order')
            ->latest()
            ->paginate(12);

        return view('imagegen.admin.pages.posts.index', compact('posts'));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('admin.posts.index');
    }

    public function store(Request $request): RedirectResponse
    {
        DashboardPost::create($this->postData($request));

        return redirect()->route('admin.posts.index')->with('status', 'Post created.');
    }

    public function edit(DashboardPost $post): View
    {
        return view('imagegen.admin.pages.posts.edit', compact('post'));
    }

    public function update(Request $request, DashboardPost $post): RedirectResponse
    {
        $data = $this->postData($request, $post);
        $post->update($data);

        return redirect()->route('admin.posts.index')->with('status', 'Post updated.');
    }

    public function destroy(DashboardPost $post): RedirectResponse
    {
        $this->deleteImage($post->image_path);
        $post->delete();

        return redirect()->route('admin.posts.index')->with('status', 'Post deleted.');
    }

    private function postData(Request $request, ?DashboardPost $post = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'image' => ['nullable', 'image', 'max:4096'],
            'paragraph' => ['nullable', 'string', 'max:5000'],
            'positive_prompt' => ['nullable', 'string', 'max:5000'],
            'negative_prompt' => ['nullable', 'string', 'max:5000'],
            'settings' => ['nullable', 'string', 'max:5000'],
            'recommendation' => ['nullable', 'string', 'max:5000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data = [
            'title' => $validated['title'],
            'paragraph' => $validated['paragraph'] ?? null,
            'positive_prompt' => $validated['positive_prompt'] ?? null,
            'negative_prompt' => $validated['negative_prompt'] ?? null,
            'settings' => $validated['settings'] ?? null,
            'recommendation' => $validated['recommendation'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_published' => $request->boolean('is_published'),
        ];

        if ($request->hasFile('image')) {
            if ($post) {
                $this->deleteImage($post->image_path);
            }

            $data['image_path'] = $this->storeImage($request->file('image'));
        }

        return $data;
    }

    private function storeImage($image): string
    {
        $dir = public_path('dashboard-posts');
        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0777, true);
        }

        $fileName = 'post_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $image->move($dir, $fileName);

        return 'dashboard-posts/' . $fileName;
    }

    private function deleteImage(?string $path): void
    {
        if (! $path) {
            return;
        }

        $fullPath = public_path($path);
        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}
