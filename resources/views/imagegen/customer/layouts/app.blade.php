<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Customer Panel')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-800">
    <div class="min-h-screen flex">
        <aside class="w-64 bg-slate-900 text-white flex flex-col shadow-xl">
            <div class="px-6 py-5 border-b border-slate-800">
                <h1 class="text-2xl font-bold">AI Studio</h1>
                <p class="text-sm text-slate-300 mt-1">Customer Panel</p>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="{{ route('customer.dashboard') }}"
                   class="block rounded-xl px-4 py-3 transition {{ request()->routeIs('customer.dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    Dashboard
                </a>
                <a href="{{ route('customer.blender-studio') }}"
                   class="block rounded-xl px-4 py-3 transition {{ request()->routeIs('customer.blender-studio') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    Blender Studio
                </a>
                <a href="{{ route('customer.input-images') }}"
                   class="block rounded-xl px-4 py-3 transition {{ request()->routeIs('customer.input-images') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    Input Images
                </a>
                <a href="{{ route('customer.ai-studio') }}"
                   class="block rounded-xl px-4 py-3 transition {{ request()->routeIs('customer.ai-studio') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    Generate Images
                </a>

                <a href="{{ route('customer.gallery') }}"
                   class="block rounded-xl px-4 py-3 transition {{ request()->routeIs('customer.gallery') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    Gallery
                </a>




            </nav>

            <div class="p-4 border-t border-slate-800">
                <div class="mb-3 text-sm text-slate-300">
                    {{ auth()->user()->name ?? 'Customer' }}
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full rounded-xl bg-rose-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-rose-600 transition">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <main class="min-w-0 flex-1 p-6">
            <div class="mb-6 rounded-2xl bg-white shadow-sm border border-slate-200 px-6 py-4">
                <h2 class="text-2xl font-bold">@yield('page_title', 'Customer Dashboard')</h2>
                <p class="text-sm text-slate-500 mt-1">@yield('page_subtitle', 'Manage your AI images')</p>
            </div>

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
