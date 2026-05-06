@extends('imagegen.admin.layouts.app')

@section('title', 'Edit Post')
@section('page_title', 'Edit Dashboard Post')
@section('page_subtitle', 'Update customer dashboard modal content')

@section('content')
<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    @if($errors->any())
        <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.posts.update', $post) }}" enctype="multipart/form-data" class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        @csrf
        @method('PUT')

        <label class="text-sm font-semibold text-slate-700">Title
            <input name="title" value="{{ old('title', $post->title) }}" required class="mt-1 w-full rounded-xl border-slate-300 text-sm">
        </label>

        <label class="text-sm font-semibold text-slate-700">Replace image
            <input name="image" type="file" accept="image/*" class="mt-1 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
        </label>

        @if($post->image_path)
            <div class="xl:col-span-2">
                <img src="{{ asset($post->image_path) }}" alt="{{ $post->title }}" class="h-52 rounded-2xl object-cover">
            </div>
        @endif

        <label class="xl:col-span-2 text-sm font-semibold text-slate-700">Paragraph
            <textarea name="paragraph" rows="4" class="mt-1 w-full rounded-xl border-slate-300 text-sm">{{ old('paragraph', $post->paragraph) }}</textarea>
        </label>

        <label class="text-sm font-semibold text-slate-700">Positive prompt
            <textarea name="positive_prompt" rows="4" class="mt-1 w-full rounded-xl border-slate-300 text-sm">{{ old('positive_prompt', $post->positive_prompt) }}</textarea>
        </label>

        <label class="text-sm font-semibold text-slate-700">Negative prompt
            <textarea name="negative_prompt" rows="4" class="mt-1 w-full rounded-xl border-slate-300 text-sm">{{ old('negative_prompt', $post->negative_prompt) }}</textarea>
        </label>

        <label class="text-sm font-semibold text-slate-700">Settings
            <textarea name="settings" rows="4" class="mt-1 w-full rounded-xl border-slate-300 text-sm">{{ old('settings', $post->settings) }}</textarea>
        </label>

        <label class="text-sm font-semibold text-slate-700">Recommendation
            <textarea name="recommendation" rows="4" class="mt-1 w-full rounded-xl border-slate-300 text-sm">{{ old('recommendation', $post->recommendation) }}</textarea>
        </label>

        <div class="flex flex-wrap items-center gap-4 xl:col-span-2">
            <label class="text-sm font-semibold text-slate-700">Sort
                <input name="sort_order" type="number" min="0" value="{{ old('sort_order', $post->sort_order) }}" class="mt-1 w-28 rounded-xl border-slate-300 text-sm">
            </label>
            <label class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $post->is_published)) class="rounded border-slate-300 text-indigo-600">
                Published
            </label>
            <button class="mt-6 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-indigo-700">Update post</button>
            <a href="{{ route('admin.posts.index') }}" class="mt-6 rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">Back</a>
        </div>
    </form>
</div>
@endsection
