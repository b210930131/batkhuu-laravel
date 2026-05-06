@extends('imagegen.admin.layouts.app')

@section('title', 'Admin Management')
@section('page_title', 'Admin Management')
@section('page_subtitle', 'Operational controls and review pages')

@section('content')
<div class="grid grid-cols-1 gap-5 xl:grid-cols-4">
    <a href="{{ route('admin.users.index') }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
        <div class="text-xs font-bold uppercase tracking-wide text-indigo-600">Users</div>
        <h3 class="mt-3 text-2xl font-bold text-slate-900">User Registry</h3>
        <p class="mt-2 text-sm leading-6 text-slate-500">Add users and control active or inactive access.</p>
    </a>
    <a href="{{ route('admin.statistics') }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
        <div class="text-xs font-bold uppercase tracking-wide text-emerald-600">Analytics</div>
        <h3 class="mt-3 text-2xl font-bold text-slate-900">Usage Statistics</h3>
        <p class="mt-2 text-sm leading-6 text-slate-500">See generated image counts, Blender usage, ControlNet input usage, and model usage.</p>
    </a>
    <a href="{{ route('admin.posts.index') }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
        <div class="text-xs font-bold uppercase tracking-wide text-amber-600">Posts</div>
        <h3 class="mt-3 text-2xl font-bold text-slate-900">Dashboard Posts</h3>
        <p class="mt-2 text-sm leading-6 text-slate-500">Create, edit, publish, and delete customer dashboard modal posts.</p>
    </a>
    <a href="{{ route('admin.gallery') }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
        <div class="text-xs font-bold uppercase tracking-wide text-violet-600">Library</div>
        <h3 class="mt-3 text-2xl font-bold text-slate-900">Output Management</h3>
        <p class="mt-2 text-sm leading-6 text-slate-500">Review generated outputs and move images between folders.</p>
    </a>
</div>
@endsection
