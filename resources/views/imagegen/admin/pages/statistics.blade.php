@extends('imagegen.admin.layouts.app')

@section('title', 'Statistics')
@section('page_title', 'User Statistics')
@section('page_subtitle', 'Generation, Blender, and ControlNet usage')

@section('content')
<div class="space-y-6">
    <section class="grid grid-cols-2 gap-4 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-wide text-slate-500">Users</div>
            <div class="mt-2 text-3xl font-bold text-slate-900">{{ $totals['users'] }}</div>
            <div class="mt-1 text-xs text-slate-500">{{ $totals['active_users'] }} active / {{ $totals['inactive_users'] }} inactive</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-wide text-slate-500">Generated</div>
            <div class="mt-2 text-3xl font-bold text-slate-900">{{ $totals['generated_images'] }}</div>
            <div class="mt-1 text-xs text-slate-500">{{ $totals['completed_images'] }} completed images</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-wide text-slate-500">Blender Studio</div>
            <div class="mt-2 text-3xl font-bold text-slate-900">{{ $totals['blender_inputs'] }}</div>
            <div class="mt-1 text-xs text-slate-500">Room render input images</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-wide text-slate-500">ControlNet</div>
            <div class="mt-2 text-3xl font-bold text-slate-900">{{ $totals['controlnet_inputs'] }}</div>
            <div class="mt-1 text-xs text-slate-500">{{ $totals['input_images'] }} total input images</div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h3 class="text-lg font-bold text-slate-900">Top Users By Usage</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3">User</th>
                            <th class="px-5 py-3">Generated</th>
                            <th class="px-5 py-3">Blender</th>
                            <th class="px-5 py-3">ControlNet</th>
                            <th class="px-5 py-3">Folders</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($users as $user)
                            <tr>
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-900">{{ $user->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $user->email }}</div>
                                </td>
                                <td class="px-5 py-4 font-semibold">{{ $user->generated_images_count }}</td>
                                <td class="px-5 py-4 font-semibold">{{ $user->blender_inputs_count }}</td>
                                <td class="px-5 py-4 font-semibold">{{ $user->controlnet_inputs_count }}</td>
                                <td class="px-5 py-4 font-semibold">{{ $user->folders_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900">Model Usage</h3>
            <div class="mt-4 space-y-3">
                @forelse($modelUsage as $model)
                    <div>
                        <div class="flex items-center justify-between gap-3 text-sm">
                            <span class="truncate font-semibold text-slate-700">{{ $model->model_name }}</span>
                            <span class="font-bold text-slate-900">{{ $model->total }}</span>
                        </div>
                        <div class="mt-2 h-2 rounded-full bg-slate-100">
                            <div class="h-2 rounded-full bg-indigo-600" style="width: {{ min(100, $totals['generated_images'] ? ($model->total / $totals['generated_images']) * 100 : 0) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No model usage yet.</p>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection
