@extends('imagegen.customer.layouts.app')

@section('title', 'My Gallery')
@section('page_title', 'My Gallery')
@section('page_subtitle', 'Your generated images')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-xl font-semibold text-slate-900">Generated Images</h3>
            <p class="text-sm text-slate-500">Only your completed images are shown here.</p>
        </div>

        <button
            id="refreshGalleryBtn"
            type="button"
            class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:from-indigo-700 hover:to-violet-700"
        >
            Refresh Gallery
        </button>
    </div>

    <div id="galleryStatus"
         class="hidden rounded-2xl border px-4 py-3 text-sm font-medium">
    </div>

    <div id="images" class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
        <div class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-white/70 px-6 py-20 text-center shadow-sm">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-2xl">
                🖼️
            </div>
            <h4 class="text-lg font-semibold text-slate-800">Loading your gallery...</h4>
            <p class="mt-2 text-sm text-slate-500">Please wait a moment.</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
function setGalleryStatus(message, type = 'info') {
    const el = document.getElementById('galleryStatus');
    if (!el) return;

    const styles = {
        info: 'border-indigo-200 bg-indigo-50 text-indigo-700',
        success: 'border-emerald-200 bg-emerald-50 text-emerald-700',
        error: 'border-rose-200 bg-rose-50 text-rose-700',
    };

    el.className = `rounded-2xl border px-4 py-3 text-sm font-medium ${styles[type] || styles.info}`;
    el.textContent = message;
    el.classList.remove('hidden');
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

async function refreshGallery() {
    const gallery = document.getElementById('images');
    const refreshBtn = document.getElementById('refreshGalleryBtn');

    if (refreshBtn) {
        refreshBtn.disabled = true;
        refreshBtn.dataset.original = refreshBtn.innerHTML;
        refreshBtn.innerHTML = 'Loading...';
        refreshBtn.classList.add('opacity-70', 'cursor-not-allowed');
    }

    try {
        const response = await fetch('/api/images', {
            headers: { 'Accept': 'application/json' }
        });

        const images = await response.json();
        console.log('customer gallery images:', images);

        if (!response.ok) {
            throw new Error(images.message || 'Failed to load gallery');
        }

        const completedImages = (images || []).filter(img => img.file_name);

        if (!completedImages.length) {
            gallery.innerHTML = `
                <div class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-20 text-center shadow-sm">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-2xl">
                        🎨
                    </div>
                    <h4 class="text-lg font-semibold text-slate-800">No images yet</h4>
                    <p class="mt-2 text-sm text-slate-500">Generate an image first, then it will appear here.</p>
                </div>
            `;
            setGalleryStatus('No completed images found yet.', 'info');
            return;
        }

        gallery.innerHTML = completedImages.map(img => {
            const imageUrl =
                `/api/comfyui/view?filename=${encodeURIComponent(img.file_name)}` +
                `&subfolder=${encodeURIComponent(img.subfolder || '')}` +
                `&type=${encodeURIComponent(img.type || 'output')}`;

            return `
                <article class="group overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                    <div class="relative aspect-square overflow-hidden bg-slate-100">
                        <img
                            src="${imageUrl}"
                            alt="Generated image"
                            class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]"
                            onclick="window.open('${imageUrl}', '_blank')"
                        >
                        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/35 to-transparent"></div>
                    </div>

                    <div class="space-y-3 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-800">${escapeHtml(img.file_name)}</p>
                                <p class="mt-1 text-xs text-slate-500">ID: ${escapeHtml(img.id)}</p>
                            </div>
                            <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-medium text-slate-600">
                                ${escapeHtml((img.type || 'output').toUpperCase())}
                            </span>
                        </div>

                        ${img.positive_prompt ? `
                            <p class="line-clamp-2 text-xs leading-5 text-slate-500">
                                ${escapeHtml(img.positive_prompt)}
                            </p>
                        ` : ''}

                        <div class="flex gap-2">
                            <button
                                type="button"
                                onclick="window.open('${imageUrl}', '_blank')"
                                class="inline-flex flex-1 items-center justify-center rounded-2xl border border-slate-300 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                            >
                                View
                            </button>

                            <button
                                type="button"
                                onclick="deleteCustomerImage(${img.id})"
                                class="inline-flex flex-1 items-center justify-center rounded-2xl bg-rose-500 px-3 py-2.5 text-xs font-semibold text-white transition hover:bg-rose-600"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </article>
            `;
        }).join('');

        setGalleryStatus(`Loaded ${completedImages.length} image(s).`, 'success');
    } catch (error) {
        console.error('gallery error:', error);
        gallery.innerHTML = `
            <div class="col-span-full rounded-3xl border border-rose-200 bg-rose-50 px-6 py-20 text-center shadow-sm">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-100 text-2xl">
                    ⚠️
                </div>
                <h4 class="text-lg font-semibold text-rose-700">Failed to load gallery</h4>
                <p class="mt-2 text-sm text-rose-600">${escapeHtml(error.message || 'Unknown error')}</p>
            </div>
        `;
        setGalleryStatus(`Gallery error: ${error.message}`, 'error');
    } finally {
        if (refreshBtn) {
            refreshBtn.disabled = false;
            refreshBtn.innerHTML = refreshBtn.dataset.original || 'Refresh Gallery';
            refreshBtn.classList.remove('opacity-70', 'cursor-not-allowed');
        }
    }
}

async function deleteCustomerImage(id) {
    const ok = confirm('Delete this image?');
    if (!ok) return;

    try {
        const response = await fetch(`/customer/api/images/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Delete failed');
        }

        setGalleryStatus('Image deleted successfully.', 'success');
        refreshGallery();
    } catch (error) {
        console.error('delete error:', error);
        setGalleryStatus(`Delete error: ${error.message}`, 'error');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    refreshGallery();
    document.getElementById('refreshGalleryBtn')?.addEventListener('click', refreshGallery);
});
</script>
@endpush
@endsection