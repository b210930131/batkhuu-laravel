@extends('imagegen.admin.layouts.app')

@section('title', 'Admin Dashboard')
@section('page_title', 'Admin Dashboard')
@section('page_subtitle', 'Platform management overview')

@section('content')
<div class="space-y-6">
    <section class="overflow-hidden rounded-3xl bg-slate-950 px-8 py-8 text-white shadow-2xl">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="inline-flex rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-indigo-100">
                    Admin Control
                </div>
                <h1 class="mt-4 text-3xl font-bold tracking-tight">Manage the full AI platform</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
                    Review generated images, manage user folders, inspect input images, and run the full admin studio.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('admin.ai-studio') }}" class="rounded-2xl bg-indigo-600 px-5 py-4 text-sm font-bold text-white transition hover:bg-indigo-700">
                    AI Studio
                </a>
                <a href="{{ route('admin.gallery') }}" class="rounded-2xl bg-white/10 px-5 py-4 text-sm font-bold text-white transition hover:bg-white/15">
                    Gallery
                </a>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="text-xs font-bold uppercase tracking-wide text-indigo-600">Customer Posts</div>
                <h2 class="mt-1 text-xl font-bold text-slate-900">Dashboard post management</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">Customer dashboard дээр зураг, title, paragraph, prompt болон тохиргооны зөвлөмжтэй modal post харуулна.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.posts.index') }}" class="rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700">
                    Post оруулах
                </a>
                <span class="rounded-xl bg-slate-100 px-4 py-3 text-sm font-bold text-slate-600">{{ $publishedPostCount ?? 0 }} published / {{ $postCount ?? 0 }} total</span>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <a href="{{ route('admin.users.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
            <div class="text-xs font-semibold uppercase tracking-wide text-indigo-600">Users</div>
            <h3 class="mt-3 text-xl font-bold text-slate-900">Хэрэглэгчийн бүртгэл</h3>
            <p class="mt-2 text-sm leading-6 text-slate-500">Шинэ хэрэглэгч нэмэх, customer/admin role харах, active inactive төлөв солих.</p>
        </a>

        <a href="{{ route('admin.statistics') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
            <div class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Statistics</div>
            <h3 class="mt-3 text-xl font-bold text-slate-900">Хэрэглэгчдийн статистик</h3>
            <p class="mt-2 text-sm leading-6 text-slate-500">Хэдэн зураг generate хийсэн, Blender Studio болон ControlNet input хэр ашигласныг харна.</p>
        </a>

        <a href="{{ route('admin.management') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
            <div class="text-xs font-semibold uppercase tracking-wide text-violet-600">Management</div>
            <h3 class="mt-3 text-xl font-bold text-slate-900">Удирдлагын хэсэг</h3>
            <p class="mt-2 text-sm leading-6 text-slate-500">User registry, usage analytics, gallery management зэрэг admin page-үүд рүү орно.</p>
        </a>
    </section>

    <section class="grid grid-cols-1 gap-5 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900">Admin Shortcuts</h3>
            <div class="mt-4 flex flex-wrap gap-3">
                <a href="{{ route('customer.dashboard') }}" class="rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">Customer Dashboard</a>
                <a href="{{ route('customer.gallery') }}" class="rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">Customer Gallery</a>
                <a href="{{ route('twenty') }}" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">Web Pages</a>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900">Platform Scope</h3>
            <ul class="mt-4 space-y-3 text-sm text-slate-600">
                <li>Admin sees all generated images and all input images.</li>
                <li>Admin can create folders for selected users.</li>
                <li>Customer dashboard content is kept separate from this page.</li>
            </ul>
        </div>
    </section>
</div>
@endsection
