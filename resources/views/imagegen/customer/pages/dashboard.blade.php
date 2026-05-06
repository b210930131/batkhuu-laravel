@extends('imagegen.customer.layouts.app')

@section('title', 'Customer Dashboard')
@section('page_title', 'Customer Dashboard')
@section('page_subtitle', 'Your AI image workspace')

@section('content')
<div class="space-y-6">
    <section class="overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-indigo-700">
                    Customer Workspace
                </div>
                <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-900">Create and organize your images</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500">
                    Generate images, prepare Blender room inputs, upload ControlNet sources, and manage your private gallery.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('customer.ai-studio') }}" class="rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700">
                    Generate Images
                </a>
                <a href="{{ route('customer.gallery') }}" class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                    My Gallery
                </a>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-5 flex items-center justify-between gap-4">
            <div>
                <div class="text-xs font-bold uppercase tracking-wide text-indigo-600">Admin Posts</div>
                <h2 class="mt-1 text-xl font-bold text-slate-900">Prompt guides and recommendations</h2>
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500">{{ $posts->count() }} posts</span>
        </div>

        <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
            @forelse($posts as $post)
                <button type="button"
                    data-post-title="{{ e($post->title) }}"
                    data-post-image="{{ $post->image_path ? asset($post->image_path) : '' }}"
                    data-post-paragraph="{{ e($post->paragraph) }}"
                    data-post-positive="{{ e($post->positive_prompt) }}"
                    data-post-negative="{{ e($post->negative_prompt) }}"
                    data-post-settings="{{ e($post->settings) }}"
                    data-post-recommendation="{{ e($post->recommendation) }}"
                    onclick="openDashboardPost(this)"
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 text-left shadow-sm transition hover:-translate-y-1 hover:bg-white hover:shadow-lg">
                    @if($post->image_path)
                        <img src="{{ asset($post->image_path) }}" alt="{{ $post->title }}" class="h-36 w-full object-cover">
                    @else
                        <div class="flex h-36 items-center justify-center bg-slate-100 text-sm font-semibold text-slate-400">No image</div>
                    @endif
                    <div class="p-4">
                        <h3 class="text-base font-bold text-slate-900">{{ $post->title }}</h3>
                        <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-500">{{ $post->paragraph }}</p>
                    </div>
                </button>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center text-sm text-slate-400 xl:col-span-3">
                    No admin posts yet.
                </div>
            @endforelse
        </div>
    </section>

    <section class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <a href="{{ route('customer.ai-studio') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
            <div class="text-xs font-semibold uppercase tracking-wide text-indigo-600">Generate</div>
            <h3 class="mt-3 text-xl font-bold text-slate-900">AI Studio</h3>
            <p class="mt-2 text-sm leading-6 text-slate-500">Use prompts, models, and ControlNet settings for new images.</p>
        </a>

        <a href="{{ route('customer.blender-studio') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
            <div class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Room Render</div>
            <h3 class="mt-3 text-xl font-bold text-slate-900">Blender Studio</h3>
            <p class="mt-2 text-sm leading-6 text-slate-500">Create a room image and save it as a ControlNet input.</p>
        </a>

        <a href="{{ route('customer.input-images') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
            <div class="text-xs font-semibold uppercase tracking-wide text-amber-600">Sources</div>
            <h3 class="mt-3 text-xl font-bold text-slate-900">Input Images</h3>
            <p class="mt-2 text-sm leading-6 text-slate-500">Browse the input images attached to your account.</p>
        </a>
    </section>

    <section class="grid grid-cols-1 gap-5 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900">My Library</h3>
            <p class="mt-2 text-sm leading-6 text-slate-500">Your gallery only shows images created by your account.</p>
            <a href="{{ route('customer.gallery') }}" class="mt-5 inline-flex rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                Open Gallery
            </a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900">Start Prompts</h3>
            <ul class="mt-4 space-y-3 text-sm text-slate-600">
                <li>Interior design with soft natural light.</li>
                <li>Modern exterior facade at golden hour.</li>
                <li>Furniture concept with realistic materials.</li>
            </ul>
        </div>
    </section>
</div>

<div id="dashboardPostModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 p-4 backdrop-blur-sm" onclick="closeDashboardPost(event)">
    <div class="mx-auto max-h-[90vh] max-w-3xl overflow-y-auto rounded-2xl bg-white shadow-2xl" onclick="event.stopPropagation()">
        <img id="dashboardPostImage" src="" alt="" class="hidden h-64 w-full object-cover">
        <div class="space-y-5 p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-xs font-bold uppercase tracking-wide text-indigo-600">Admin post</div>
                    <h3 id="dashboardPostTitle" class="mt-1 text-2xl font-bold text-slate-900"></h3>
                </div>
                <button type="button" onclick="closeDashboardPost()" class="rounded-xl border border-slate-200 px-3 py-1.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50">Close</button>
            </div>
            <p id="dashboardPostParagraph" class="text-sm leading-7 text-slate-600"></p>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div class="rounded-2xl bg-emerald-50 p-4">
                    <div class="text-xs font-bold uppercase tracking-wide text-emerald-700">Positive prompt</div>
                    <p id="dashboardPostPositive" class="mt-2 whitespace-pre-wrap text-sm leading-6 text-slate-700"></p>
                </div>
                <div class="rounded-2xl bg-rose-50 p-4">
                    <div class="text-xs font-bold uppercase tracking-wide text-rose-700">Negative prompt</div>
                    <p id="dashboardPostNegative" class="mt-2 whitespace-pre-wrap text-sm leading-6 text-slate-700"></p>
                </div>
            </div>

            <div class="rounded-2xl bg-slate-50 p-4">
                <div class="text-xs font-bold uppercase tracking-wide text-slate-500">Settings</div>
                <p id="dashboardPostSettings" class="mt-2 whitespace-pre-wrap text-sm leading-6 text-slate-700"></p>
            </div>

            <div class="rounded-2xl bg-indigo-50 p-4">
                <div class="text-xs font-bold uppercase tracking-wide text-indigo-700">Recommendation</div>
                <p id="dashboardPostRecommendation" class="mt-2 whitespace-pre-wrap text-sm leading-6 text-slate-700"></p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function postValue(button, name) {
    return button.dataset[name] || 'Not provided.';
}

function openDashboardPost(button) {
    const image = button.dataset.postImage || '';
    const imageEl = document.getElementById('dashboardPostImage');
    imageEl.src = image;
    imageEl.classList.toggle('hidden', !image);

    document.getElementById('dashboardPostTitle').textContent = postValue(button, 'postTitle');
    document.getElementById('dashboardPostParagraph').textContent = postValue(button, 'postParagraph');
    document.getElementById('dashboardPostPositive').textContent = postValue(button, 'postPositive');
    document.getElementById('dashboardPostNegative').textContent = postValue(button, 'postNegative');
    document.getElementById('dashboardPostSettings').textContent = postValue(button, 'postSettings');
    document.getElementById('dashboardPostRecommendation').textContent = postValue(button, 'postRecommendation');

    document.getElementById('dashboardPostModal').classList.remove('hidden');
}

function closeDashboardPost(event) {
    if (event && event.target.id !== 'dashboardPostModal') return;
    document.getElementById('dashboardPostModal').classList.add('hidden');
}
</script>
@endpush
