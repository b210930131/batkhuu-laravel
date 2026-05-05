@php
    $isAdmin = auth()->check() && auth()->user()->role === 'admin';
@endphp

<div class="space-y-8">
    <!-- Top hero -->
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-white shadow-2xl">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(99,102,241,0.22),transparent_28%),radial-gradient(circle_at_bottom_left,rgba(168,85,247,0.18),transparent_30%)]"></div>

        <div class="relative px-8 py-10 md:px-10 md:py-12">
            <div class="flex flex-col gap-8 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-semibold tracking-wide text-indigo-100 backdrop-blur">
                        AI IMAGE GENERATION PLATFORM
                    </div>

                    <h1 class="mt-4 text-3xl font-bold tracking-tight md:text-5xl">
                        Онлайн AI платформын
                        <span class="bg-gradient-to-r from-white to-indigo-300 bg-clip-text text-transparent">
                            удирдлагын самбар
                        </span>
                    </h1>

                    <p class="mt-4 max-w-xl text-sm leading-6 text-slate-300 md:text-base">
                        Stable Diffusion, ControlNet болон gallery хэсгүүд рүү
                        хурдан шилжих, турших, удирдах зориулалттай нүүр хуудас.
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ route('customer.ai-studio') }}"
                           class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-950/30 transition hover:bg-indigo-700">
                            Customer Studio
                        </a>

                        <a href="{{ route('customer.gallery') }}"
                           class="inline-flex items-center justify-center rounded-xl border border-white/15 bg-white/5 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                            My Gallery
                        </a>

                        @if ($isAdmin)
                            <a href="{{ route('admin.ai-studio') }}"
                               class="inline-flex items-center justify-center rounded-xl border border-indigo-300/20 bg-slate-800/70 px-5 py-3 text-sm font-semibold text-slate-100 transition hover:bg-slate-700">
                                Admin AI Studio
                            </a>
                        @endif
                    </div>
                </div>

                <div class="grid w-full max-w-xl grid-cols-2 gap-4">
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-5 backdrop-blur">
                        <div class="text-xs font-medium uppercase tracking-wider text-slate-300">Models</div>
                        <div class="mt-3 text-3xl font-bold">SD + CN</div>
                        <div class="mt-1 text-sm text-slate-300">Prompt + Control image workflow</div>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/10 p-5 backdrop-blur">
                        <div class="text-xs font-medium uppercase tracking-wider text-slate-300">Role Access</div>
                        <div class="mt-3 text-3xl font-bold">{{ $isAdmin ? 'Admin' : 'Customer' }}</div>
                        <div class="mt-1 text-sm text-slate-300">{{ $isAdmin ? 'Platform management access' : 'Private image workspace' }}</div>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/10 p-5 backdrop-blur">
                        <div class="text-xs font-medium uppercase tracking-wider text-slate-300">Gallery</div>
                        <div class="mt-3 text-3xl font-bold">Private</div>
                        <div class="mt-1 text-sm text-slate-300">Each customer sees own images</div>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/10 p-5 backdrop-blur">
                        <div class="text-xs font-medium uppercase tracking-wider text-slate-300">UI</div>
                        <div class="mt-3 text-3xl font-bold">Tailwind</div>
                        <div class="mt-1 text-sm text-slate-300">Clean spacing and alignment</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main cards -->
    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <!-- Card 1 -->
        <article class="group flex h-full flex-col rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
            <div class="inline-flex w-fit items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">
                Stable Diffusion
            </div>

            <h3 class="mt-4 text-2xl font-bold tracking-tight text-slate-900">
                Prompt ашиглан зураг үүсгэх
            </h3>

            <p class="mt-3 text-sm leading-6 text-slate-600">
                Text prompt оруулж, generation pipeline ажиллуулж, customer panel дээрээс
                үр дүнгээ хянах үндсэн хэсэг.
            </p>

            <div class="mt-6 grid grid-cols-3 gap-3 rounded-2xl bg-slate-50 p-4 text-center">
                <div>
                    <div class="text-lg font-bold text-slate-900">10+ models</div>
                    <div class="text-xs text-slate-500">Diffusion</div>
                </div>
                <div>
                    <div class="text-lg font-bold text-slate-900">Fast</div>
                    <div class="text-xs text-slate-500">Workflow</div>
                </div>
                <div>
                    <div class="text-lg font-bold text-slate-900">Live</div>
                    <div class="text-xs text-slate-500">Generation</div>
                </div>
            </div>

            <div class="mt-auto pt-6 flex flex-wrap gap-3">
                <a href="{{ route('customer.ai-studio') }}"
                   class="inline-flex flex-1 items-center justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700">
                    Open Studio
                </a>

                @if ($isAdmin)
                    <a href="{{ route('admin.ai-studio') }}"
                       class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Admin
                    </a>
                @endif
            </div>
        </article>

        <!-- Card 2 -->
        <article class="group flex h-full flex-col rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
            <div class="inline-flex w-fit items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                ControlNet
            </div>

            <h3 class="mt-4 text-2xl font-bold tracking-tight text-slate-900">
                Blender ашиглан 3D орчны өрөөний зураг үүсгэх
            </h3>

            <p class="mt-3 text-sm leading-6 text-slate-600">
                Өрөөний зураг шинээр загварчилж үүсгэнэ.
            </p>

            <div class="mt-6 space-y-3 rounded-2xl bg-slate-50 p-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Camerview</span>
                    <span class="font-semibold text-slate-900">Placement</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Blender Integration</span>
                    <span class="font-semibold text-slate-900">Supported</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Structure</span>
                    <span class="font-semibold text-slate-900">Ready</span>
                </div>
            </div>

            <div class="mt-auto pt-6 flex flex-wrap gap-3">
                <a href="{{ route('admin.blender-studio') }}"
                   class="inline-flex flex-1 items-center justify-center rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Start Creating
                </a>

                <a href="{{ route('customer.gallery') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Gallery
                </a>
            </div>
        </article>

        <!-- Card 3 -->
        <article class="group flex h-full flex-col rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
            <div class="inline-flex w-fit items-center rounded-full bg-violet-100 px-3 py-1 text-xs font-semibold text-violet-700">
                Web Pages
            </div>

            <h3 class="mt-4 text-2xl font-bold tracking-tight text-slate-900">
                Веб хуудсуудын дизайн
            </h3>

            <p class="mt-3 text-sm leading-6 text-slate-600">
                UI жишээ, дизайны демо болон тусдаа хуудсууд руу орж турших хэсэг.
            </p>

            <div class="mt-6 rounded-2xl bg-gradient-to-br from-violet-50 to-indigo-50 p-4">
                <div class="text-sm font-medium text-slate-700">Preview Collection</div>
                <div class="mt-2 text-3xl font-bold text-slate-900">25 pages</div>
                <div class="mt-1 text-sm text-slate-500">Layout, cards, sections, examples</div>
            </div>

            <div class="mt-auto pt-6 flex flex-wrap gap-3">
                <a href="{{ route('twenty') }}"
                   class="inline-flex flex-1 items-center justify-center rounded-xl bg-violet-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-violet-700">
                    Орж үзэх
                </a>

                <button type="button"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    More Info
                </button>
            </div>
        </article>
    </section>

    <!-- Bottom info -->
    <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-900">Quick Navigation</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">
                Studio болон gallery хуудсууд руу шууд шилжих холбоосууд.
            </p>

            <div class="mt-5 flex flex-wrap gap-3">
                <a href="{{ route('dashboard') }}"
                   class="rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-200">
                    Dashboard
                </a>

                <a href="{{ route('customer.ai-studio') }}"
                   class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700">
                    Customer
                </a>

                <a href="{{ route('customer.gallery') }}"
                   class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-emerald-700">
                    Gallery
                </a>

                @if ($isAdmin)
                    <a href="{{ route('admin.ai-studio') }}"
                       class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                        Admin
                    </a>
                @endif
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-900">Start prompt</h3>
            <ul class="mt-4 space-y-3 text-sm text-slate-600">
                <li class="flex items-start gap-3">
                    <span class="mt-1 h-2 w-2 rounded-full bg-indigo-500"></span>
                    Exterior design
                    <!-- <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs">flex h-full flex-col</code> -->
                </li>
                <li class="flex items-start gap-3">
                    <span class="mt-1 h-2 w-2 rounded-full bg-emerald-500"></span>
                    Interior design
                </li>
                <li class="flex items-start gap-3">
                    <span class="mt-1 h-2 w-2 rounded-full bg-violet-500"></span>
                    Furniture design
                </li>
            </ul>
        </div>
    </section>
</div>