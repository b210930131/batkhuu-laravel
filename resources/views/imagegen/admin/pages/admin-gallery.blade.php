@extends('imagegen.admin.layouts.app')

@section('title', 'Admin Gallery')
@section('page_title', 'Admin Gallery')
@section('page_subtitle', 'View and manage all generated images')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">🖼️ Admin Gallery</h1>
            <p class="mt-1 text-sm text-slate-500">All generated images from all users</p>
        </div>

        <button
            id="refreshGalleryBtn"
            type="button"
            class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:from-indigo-700 hover:to-violet-700"
        >
            Refresh
        </button>
    </div>

    <div id="images" class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
        <div class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-20 text-center shadow-sm">
            Loading admin gallery...
        </div>
    </div>
</div>

@push('scripts')
<script>
async function refreshAdminGallery() {
    const gallery = document.getElementById('images');
    const response = await fetch('/admin/api/images', { headers: { 'Accept': 'application/json' } });
    const images = await response.json();

    if (!images.length) {
        gallery.innerHTML = `<div class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-20 text-center shadow-sm">No images found.</div>`;
        return;
    }

    gallery.innerHTML = images.map(img => {
        const imageUrl = img.file_name
            ? `/api/comfyui/view?filename=${encodeURIComponent(img.file_name)}&subfolder=${encodeURIComponent(img.subfolder || '')}&type=${encodeURIComponent(img.type || 'output')}`
            : '';

        return `
            <article class="group overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                <div class="relative aspect-square overflow-hidden bg-slate-100">
                    ${img.file_name
                        ? `<img src="${imageUrl}" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]" onclick="window.open('${imageUrl}', '_blank')">`
                        : `<div class="flex h-full w-full items-center justify-center text-3xl text-slate-400">🎨</div>`
                    }
                </div>

                <div class="space-y-3 p-4">
                    <p class="truncate text-sm font-semibold text-slate-800">${img.user?.name || 'Unknown'}</p>
                    <p class="line-clamp-2 text-xs leading-5 text-slate-500">${img.positive_prompt || 'No prompt'}</p>

                    <button
                        type="button"
                        onclick="deleteAdminImage(${img.id})"
                        class="w-full rounded-2xl bg-rose-500 px-3 py-2.5 text-xs font-semibold text-white transition hover:bg-rose-600"
                    >
                        Delete
                    </button>
                </div>
            </article>
        `;
    }).join('');
}

async function deleteAdminImage(id) {
    if (!confirm('Delete this image?')) return;

    const response = await fetch(`/admin/gallery/delete/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    });

    const result = await response.json();
    if (response.ok && result.success) refreshAdminGallery();
}

document.addEventListener('DOMContentLoaded', () => {
    refreshAdminGallery();
    document.getElementById('refreshGalleryBtn')?.addEventListener('click', refreshAdminGallery);
});
</script>
@endpush
@endsection