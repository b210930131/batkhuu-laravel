@extends('imagegen.admin.layouts.app')

@section('title', 'Users')
@section('page_title', 'User Management')
@section('page_subtitle', 'Create users and control active access')

@section('content')
<div class="space-y-6">
    @if(session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700">{{ $errors->first() }}</div>
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-bold text-slate-900">Add User</h3>
        <form method="POST" action="{{ route('admin.users.store') }}" class="mt-5 grid grid-cols-1 gap-4 xl:grid-cols-[1fr_1fr_1fr_160px_140px_auto]">
            @csrf
            <input name="name" value="{{ old('name') }}" placeholder="Name" required class="rounded-xl border-slate-300 text-sm">
            <input name="email" value="{{ old('email') }}" type="email" placeholder="Email" required class="rounded-xl border-slate-300 text-sm">
            <input name="password" type="password" placeholder="Password" required class="rounded-xl border-slate-300 text-sm">
            <select name="role" class="rounded-xl border-slate-300 text-sm">
                <option value="customer">Customer</option>
                <option value="admin">Admin</option>
            </select>
            <label class="flex items-center gap-2 rounded-xl border border-slate-300 px-3 text-sm font-semibold text-slate-700">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-indigo-600">
                Active
            </label>
            <button class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-indigo-700">Add</button>
        </form>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4">
            <h3 class="text-lg font-bold text-slate-900">Registered Users</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">User</th>
                        <th class="px-5 py-3">Role</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Generated</th>
                        <th class="px-5 py-3">Blender</th>
                        <th class="px-5 py-3">ControlNet</th>
                        <th class="px-5 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $user)
                        <tr>
                            <td class="px-5 py-4">
                                <div class="font-bold text-slate-900">{{ $user->name }}</div>
                                <div class="text-xs text-slate-500">{{ $user->email }}</div>
                            </td>
                            <td class="px-5 py-4 capitalize text-slate-700">{{ $user->role ?? 'customer' }}</td>
                            <td class="px-5 py-4">
                                @if($user->is_active)
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">Active</span>
                                @else
                                    <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-bold text-rose-700">Inactive</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 font-semibold text-slate-800">{{ $user->generated_images_count }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-800">{{ $user->blender_inputs_count }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-800">{{ $user->controlnet_inputs_count }}</td>
                            <td class="px-5 py-4">
                                <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="rounded-xl px-4 py-2 text-xs font-bold text-white transition {{ $user->is_active ? 'bg-rose-600 hover:bg-rose-700' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-6 py-4">
            {{ $users->links() }}
        </div>
    </section>
</div>
@endsection
