<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-800">
    <div class="min-h-screen flex">
        <aside class="w-72 bg-slate-950 text-white flex flex-col shadow-2xl">
            <div class="px-6 py-6 border-b border-white/10">
                <h1 class="text-2xl font-bold tracking-tight">AI Studio</h1>
                <p class="mt-1 text-sm text-slate-300">Admin Panel</p>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="{{ route('admin.dashboard') }}"
                   class="block rounded-2xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    Dashboard
                </a>

                <a href="{{ route('admin.ai-studio') }}"
                   class="block rounded-2xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('admin.ai-studio') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    Admin Studio
                </a>

                <a href="{{ route('admin.gallery') }}"
                   class="block rounded-2xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('admin.gallery') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    Admin Gallery
                </a>

                <a href="{{ route('customer.dashboard') }}"
                   class="block rounded-2xl px-4 py-3 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                    Customer Dashboard
                </a>

                <a href="{{ route('customer.gallery') }}"
                   class="block rounded-2xl px-4 py-3 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                    Customer Gallery
                </a>
            </nav>

            <div class="border-t border-white/10 p-4">
                <div class="mb-3 text-sm text-slate-300">
                    {{ auth()->user()->name ?? 'Admin' }}
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full rounded-2xl bg-rose-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-600">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 p-6">
            <div class="mb-6 rounded-3xl bg-white px-6 py-5 shadow-sm ring-1 ring-slate-200">
                <h2 class="text-2xl font-bold text-slate-900">@yield('page_title', 'Admin Dashboard')</h2>
                <p class="mt-1 text-sm text-slate-500">@yield('page_subtitle', 'Manage the platform')</p>
            </div>

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>