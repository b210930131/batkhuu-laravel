<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneratedImage;
use App\Models\InputImage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminManagementController extends Controller
{
    public function users(): View
    {
        $users = User::query()
            ->withCount([
                'generatedImages',
                'inputImages',
                'folders',
                'inputImages as blender_inputs_count' => fn ($query) => $query->where('source_type', 'blender'),
                'inputImages as controlnet_inputs_count' => fn ($query) => $query->where('source_type', 'controlnet'),
            ])
            ->latest()
            ->paginate(15);

        return view('imagegen.admin.pages.users', compact('users'));
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'customer'])],
            'is_active' => ['nullable', 'boolean'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active', true),
            'blocked_at' => $request->boolean('is_active', true) ? null : now(),
        ]);

        return back()->with('status', 'User created successfully.');
    }

    public function toggleUser(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return back()->withErrors(['user' => 'You cannot deactivate your own admin account.']);
        }

        $active = ! (bool) $user->is_active;
        $user->update([
            'is_active' => $active,
            'blocked_at' => $active ? null : now(),
        ]);

        return back()->with('status', $active ? 'User activated.' : 'User deactivated.');
    }

    public function statistics(): View
    {
        $totals = [
            'users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
            'generated_images' => GeneratedImage::count(),
            'completed_images' => GeneratedImage::whereNotNull('file_name')->count(),
            'input_images' => InputImage::count(),
            'blender_inputs' => InputImage::where('source_type', 'blender')->count(),
            'controlnet_inputs' => InputImage::where('source_type', 'controlnet')->count(),
        ];

        $users = User::query()
            ->withCount([
                'generatedImages',
                'inputImages',
                'folders',
                'inputImages as blender_inputs_count' => fn ($query) => $query->where('source_type', 'blender'),
                'inputImages as controlnet_inputs_count' => fn ($query) => $query->where('source_type', 'controlnet'),
            ])
            ->orderByDesc('generated_images_count')
            ->limit(20)
            ->get();

        $modelUsage = GeneratedImage::query()
            ->selectRaw('COALESCE(model_used, "Unknown") as model_name, COUNT(*) as total')
            ->groupBy('model_name')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        return view('imagegen.admin.pages.statistics', compact('totals', 'users', 'modelUsage'));
    }

    public function management(): View
    {
        return view('imagegen.admin.pages.management');
    }
}
