@extends('imagegen.admin.layouts.app')

@section('title', 'Dashboard Posts')
@section('page_title', 'Dashboard Posts')
@section('page_subtitle', 'Create modal posts shown on the customer dashboard')

@section('content')
<div class="space-y-6">
    @if(session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700">{{ $errors->first() }}</div>
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-2">
            <div class="text-xs font-bold uppercase tracking-wide text-indigo-600">Create post</div>
            <h3 class="text-xl font-bold text-slate-900">Customer dashboard modal content</h3>
        </div>

        <form method="POST" action="{{ route('admin.posts.store') }}" enctype="multipart/form-data" class="mt-6 grid grid-cols-1 gap-4 xl:grid-cols-2">
            @csrf
            <label class="text-sm font-semibold text-slate-700">Title
                <input name="title" value="{{ old('title') }}" required class="mt-1 w-full rounded-xl border-slate-300 text-sm">
            </label>

            <label class="text-sm font-semibold text-slate-700">Image
                <input name="image" type="file" accept="image/*" class="mt-1 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
            </label>

            <label class="xl:col-span-2 text-sm font-semibold text-slate-700">Paragraph
                <textarea name="paragraph" rows="4" class="mt-1 w-full rounded-xl border-slate-300 text-sm">{{ old('paragraph') }}</textarea>
            </label>

            <label class="text-sm font-semibold text-slate-700">Positive prompt
                <textarea name="positive_prompt" rows="4" class="mt-1 w-full rounded-xl border-slate-300 text-sm">{{ old('positive_prompt') }}</textarea>
            </label>

            <label class="text-sm font-semibold text-slate-700">Negative prompt
                <textarea name="negative_prompt" rows="4" class="mt-1 w-full rounded-xl border-slate-300 text-sm">{{ old('negative_prompt') }}</textarea>
            </label>

            <label class="text-sm font-semibold text-slate-700">Settings
                <textarea name="settings" rows="4" placeholder="Model, steps, CFG, size, sampler..." class="mt-1 w-full rounded-xl border-slate-300 text-sm">{{ old('settings') }}</textarea>
            </label>

            <label class="text-sm font-semibold text-slate-700">Recommendation
                <textarea name="recommendation" rows="4" placeholder="How customers should use this prompt or setup." class="mt-1 w-full rounded-xl border-slate-300 text-sm">{{ old('recommendation') }}</textarea>
            </label>

            <div class="flex flex-wrap items-center gap-4 xl:col-span-2">
                <label class="text-sm font-semibold text-slate-700">Sort
                    <input name="sort_order" type="number" min="0" value="{{ old('sort_order', 0) }}" class="mt-1 w-28 rounded-xl border-slate-300 text-sm">
                </label>
                <label class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                    <input type="checkbox" name="is_published" value="1" checked class="rounded border-slate-300 text-indigo-600">
                    Published
                </label>
                <button class="mt-6 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-indigo-700">Create post</button>
            </div>
        </form>
    </section>

    <section class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        @forelse($posts as $post)
            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                @if($post->image_path)
                    <img src="{{ asset($post->image_path) }}" alt="{{ $post->title }}" class="h-44 w-full object-cover">
                @else
                    <div class="flex h-44 items-center justify-center bg-slate-100 text-sm font-semibold text-slate-400">No image</div>
                @endif
                <div class="space-y-3 p-5">
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="text-lg font-bold text-slate-900">{{ $post->title }}</h3>
                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $post->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $post->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </div>
                    <p class="line-clamp-3 text-sm leading-6 text-slate-500">{{ $post->paragraph }}</p>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.posts.edit', $post) }}" class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white transition hover:bg-slate-800">Edit</a>
                        <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm('Delete this post?')">
                            @csrf
                            @method('DELETE')
                            <button class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-rose-700">Delete</button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center text-slate-400 xl:col-span-3">
                No posts yet.
            </div>
        @endforelse
    </section>

    {{ $posts->links() }}
</div>
@endsection
