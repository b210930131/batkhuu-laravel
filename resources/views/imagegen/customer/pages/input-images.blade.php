@extends('imagegen.customer.layouts.app')

@section('title', 'Input Images')
@section('page_title', 'Input Images')
@section('page_subtitle', 'Your uploaded ControlNet/input images')

@section('content')
<div class="space-y-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">My Input Images</h1>
            <p class="mt-1 text-sm text-slate-500">Images you uploaded for image guidance.</p>
        </div>

        <button id="refreshInputImagesBtn" type="button"
            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700">
            Refresh
        </button>
    </div>

    <div id="inputImagesStatus" class="hidden rounded-2xl border px-4 py-3 text-sm font-medium"></div>

    <div class="flex flex-col gap-4 lg:flex-row">
        <aside class="shrink-0 self-start rounded-2xl border border-slate-200 bg-white p-5 shadow-sm" style="width: 280px;">
            <div class="mb-4">
                <div class="inline-flex w-fit items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">
                    Input
                </div>
                <h3 class="mt-3 text-lg font-bold tracking-tight text-slate-900">Image Sources</h3>
            </div>

            <div class="space-y-2">
                <button type="button" data-source-filter="all"
                    class="source-filter flex w-full items-center justify-between rounded-xl bg-slate-50 px-3 py-2.5 text-left text-sm font-semibold transition hover:bg-slate-100">
                    <span>All inputs</span>
                    <span id="allCount" class="text-xs text-slate-500">0</span>
                </button>
                <button type="button" data-source-filter="controlnet"
                    class="source-filter flex w-full items-center justify-between rounded-xl bg-slate-50 px-3 py-2.5 text-left text-sm font-semibold transition hover:bg-slate-100">
                    <span>ControlNet</span>
                    <span id="controlnetCount" class="text-xs text-slate-500">0</span>
                </button>
            </div>
        </aside>

        <section class="min-w-0 flex-1">
            <div id="inputImages" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-14 text-center shadow-sm">
                    Loading input images...
                </div>
            </div>
        </section>
    </div>
</div>

<div id="inputImageModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 p-4 backdrop-blur-sm">
    <div class="mx-auto flex max-h-[92vh] max-w-6xl overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="flex min-w-0 flex-1 items-center justify-center bg-slate-950 p-4">
            <img id="modalInputImage" src="" alt="Input image" class="max-h-[84vh] max-w-full rounded-xl object-contain">
        </div>

        <aside class="w-96 shrink-0 overflow-y-auto border-l border-slate-200 p-5">
            <div class="mb-4 flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h3 id="modalTitle" class="truncate text-lg font-bold text-slate-900">Input details</h3>
                    <p id="modalMeta" class="mt-1 text-xs text-slate-500"></p>
                </div>
                <button type="button" onclick="closeInputImageModal()"
                    class="rounded-xl border border-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Close
                </button>
            </div>

            <div id="modalDetails" class="space-y-4 text-sm"></div>
        </aside>
    </div>
</div>

@push('scripts')
<script>
let inputImages = [];
let activeSource = 'all';

function setInputImagesStatus(message, type = 'info') {
    const el = document.getElementById('inputImagesStatus');
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

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]').content;
}

function detailBlock(label, value) {
    const text = value ? escapeHtml(value) : 'Empty';
    return `
        <div>
            <div class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-500">${label}</div>
            <div class="rounded-xl bg-slate-50 p-3 text-sm leading-6 text-slate-700">${text}</div>
        </div>
    `;
}

function visibleInputImages() {
    if (activeSource === 'all') return inputImages;
    return inputImages.filter(img => img.source_type === activeSource);
}

function renderSourceFilters() {
    document.getElementById('allCount').textContent = inputImages.length;
    document.getElementById('controlnetCount').textContent = inputImages.filter(img => img.source_type === 'controlnet').length;

    document.querySelectorAll('.source-filter').forEach(button => {
        const active = activeSource === button.dataset.sourceFilter;
        button.className = `source-filter flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-left text-sm font-semibold transition ${active ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100'}`;
    });
}

function openInputImageModal(id, imageUrl) {
    const img = inputImages.find(item => Number(item.id) === Number(id));
    if (!img) return;

    document.getElementById('modalInputImage').src = imageUrl;
    document.getElementById('modalTitle').textContent = img.file_name || 'Input image';
    document.getElementById('modalMeta').textContent = [`ID ${img.id}`, img.source_type || 'input'].filter(Boolean).join(' - ');
    document.getElementById('modalDetails').innerHTML = [
        detailBlock('Preprocessor', img.preprocessor),
        detailBlock('Source type', img.source_type),
        detailBlock('Mime type', img.mime_type),
        detailBlock('Uploaded', img.created_at),
        detailBlock('File', img.file_name),
    ].join('');

    document.getElementById('inputImageModal').classList.remove('hidden');
    document.getElementById('inputImageModal').classList.add('flex');
}

function closeInputImageModal() {
    const modal = document.getElementById('inputImageModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.getElementById('modalInputImage').src = '';
}

function renderInputImages() {
    const grid = document.getElementById('inputImages');
    const images = visibleInputImages();

    if (!images.length) {
        grid.innerHTML = '<div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-14 text-center shadow-sm">No input images found.</div>';
        return;
    }

    grid.innerHTML = images.map(img => {
        const imageUrl = `/${img.path}`;
        return `
            <article class="group overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                <div class="relative aspect-square overflow-hidden bg-slate-100">
                    <img src="${imageUrl}" alt="${escapeHtml(img.file_name)}"
                        onclick="openInputImageModal(${img.id}, this.src)"
                        class="h-full w-full cursor-pointer object-cover transition duration-300 group-hover:scale-[1.03]">
                </div>
                <div class="space-y-2 p-4">
                    <p class="truncate text-sm font-semibold text-slate-800">${escapeHtml(img.file_name)}</p>
                    <p class="text-xs text-slate-500">${escapeHtml(img.preprocessor || 'controlnet')}</p>
                    <button type="button" onclick="deleteInputImage(${img.id})"
                        class="w-full rounded-xl bg-rose-500 px-3 py-2 text-xs font-semibold text-white transition hover:bg-rose-600">
                        Delete
                    </button>
                </div>
            </article>
        `;
    }).join('');
}

async function deleteInputImage(id) {
    if (!confirm('Delete this input image?')) return;

    try {
        const response = await fetch(`/customer/api/input-images/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' }
        });
        const result = await response.json();
        if (!response.ok) throw new Error(result.message || 'Delete failed');
        await refreshInputImages();
        setInputImagesStatus('Input image deleted.', 'success');
    } catch (error) {
        setInputImagesStatus(`Delete error: ${error.message}`, 'error');
    }
}

async function refreshInputImages() {
    const refreshBtn = document.getElementById('refreshInputImagesBtn');
    refreshBtn.disabled = true;
    refreshBtn.textContent = 'Loading...';

    try {
        const response = await fetch('/customer/api/input-images', { headers: { 'Accept': 'application/json' } });
        const images = await response.json();
        if (!response.ok) throw new Error(images.message || 'Failed to load input images');
        inputImages = images || [];
        renderSourceFilters();
        renderInputImages();
        setInputImagesStatus(`Loaded ${visibleInputImages().length} input image(s).`, 'success');
    } catch (error) {
        setInputImagesStatus(`Input image error: ${error.message}`, 'error');
    } finally {
        refreshBtn.disabled = false;
        refreshBtn.textContent = 'Refresh';
    }
}

document.addEventListener('keydown', event => {
    if (event.key === 'Escape') closeInputImageModal();
});

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('inputImageModal')?.addEventListener('click', event => {
        if (event.target === event.currentTarget) closeInputImageModal();
    });
    document.querySelectorAll('[data-source-filter]').forEach(button => {
        button.addEventListener('click', () => {
            activeSource = button.dataset.sourceFilter;
            renderSourceFilters();
            renderInputImages();
        });
    });
    document.getElementById('refreshInputImagesBtn')?.addEventListener('click', refreshInputImages);
    refreshInputImages();
});
</script>
@endpush
@endsection
