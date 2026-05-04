@extends('imagegen.admin.layouts.app')

@section('title', 'Admin Gallery')
@section('page_title', 'Admin Gallery')
@section('page_subtitle', 'Manage all generated images and user folders')

@section('content')
<div class="space-y-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Admin File Manager</h1>
            <p class="mt-1 text-sm text-slate-500">Create folders for users, filter images, and move files.</p>
        </div>

        <button id="refreshGalleryBtn" type="button"
            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700">
            Refresh
        </button>
    </div>

    <div id="galleryStatus" class="hidden rounded-2xl border px-4 py-3 text-sm font-medium"></div>

    <div class="flex flex-col gap-4 xl:flex-row">
        <aside class="shrink-0 self-start rounded-2xl border border-slate-200 bg-white p-5 shadow-sm" style="width: 340px;">
            <div class="mb-4">
                <div class="inline-flex w-fit items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">
                    User Folder
                </div>
                <h3 class="mt-3 text-lg font-bold tracking-tight text-slate-900">Library Control</h3>
            </div>

            <form id="folderForm" class="rounded-2xl bg-slate-50 p-3">
                <label for="folderName" class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">New folder</label>
                <div class="flex items-center gap-2">
                    <input id="folderUserId" name="user_id" type="number" min="1" placeholder="User"
                        class="w-20 rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <input id="folderName" name="folder_name" type="text" maxlength="80" placeholder="Name"
                        class="min-w-0 flex-1 rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button type="submit"
                        class="shrink-0 rounded-xl bg-indigo-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">
                        Add
                    </button>
                </div>
            </form>

            <div class="mt-4 space-y-2">
                <button type="button" data-folder-filter="all"
                    class="folder-filter flex w-full items-center justify-between rounded-xl bg-slate-50 px-3 py-2.5 text-left text-sm font-semibold transition hover:bg-slate-100">
                    <span>All images</span>
                    <span id="allCount" class="text-xs text-slate-500">0</span>
                </button>
                <button type="button" data-folder-filter="unfiled"
                    class="folder-filter flex w-full items-center justify-between rounded-xl bg-slate-50 px-3 py-2.5 text-left text-sm font-semibold transition hover:bg-slate-100">
                    <span>Unfiled</span>
                    <span id="unfiledCount" class="text-xs text-slate-500">0</span>
                </button>
            </div>

            <div class="border-t border-slate-200 pt-3">
                <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">All folders</div>
                <div id="folderList" class="space-y-1.5"></div>
            </div>
        </aside>

        <section class="min-w-0 flex-1">
            <div id="images" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-14 text-center shadow-sm">
                    Loading admin gallery...
                </div>
            </div>
        </section>
    </div>
</div>


<div id="imageModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 p-4 backdrop-blur-sm">
    <div class="mx-auto flex max-h-[92vh] max-w-6xl overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="flex min-w-0 flex-1 items-center justify-center bg-slate-950 p-4">
            <img id="modalImage" src="" alt="Generated image" class="max-h-[84vh] max-w-full rounded-xl object-contain">
        </div>

        <aside class="w-96 shrink-0 overflow-y-auto border-l border-slate-200 p-5">
            <div class="mb-4 flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h3 id="modalTitle" class="truncate text-lg font-bold text-slate-900">Image details</h3>
                    <p id="modalMeta" class="mt-1 text-xs text-slate-500"></p>
                </div>
                <button type="button" onclick="closeImageModal()"
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
let galleryImages = [];
let galleryFolders = [];
let activeFolder = 'all';

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]').content;
}

function setGalleryStatus(message, type = 'info') {
    const el = document.getElementById('galleryStatus');
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

function ownerLabel(image) {
    return image.user?.name || `User #${image.user_id}`;
}

function folderLabel(folder) {
    return `${folder.name} - ${folder.user?.name || `User #${folder.user_id}`}`;
}

function compatibleFolders(image) {
    return galleryFolders.filter(folder => Number(folder.user_id) === Number(image.user_id));
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

function openImageModal(id, imageUrl) {
    const img = galleryImages.find(item => Number(item.id) === Number(id));
    if (!img) return;

    const folder = galleryFolders.find(item => Number(item.id) === Number(img.gallery_folder_id));
    const modal = document.getElementById('imageModal');
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('modalTitle').textContent = img.file_name || 'Generated image';
    document.getElementById('modalMeta').textContent = [
        `ID ${img.id}`,
        folder ? folder.name : 'Unfiled',
        img.user?.name || null,
    ].filter(Boolean).join(' · ');

    document.getElementById('modalDetails').innerHTML = [
        detailBlock('Original', img.original_prompt),
        detailBlock('Canonical', img.canonical_prompt || img.positive_prompt),
        detailBlock('Positive prompt', img.positive_prompt),
        detailBlock('Model', img.model_used),
        detailBlock('Size', img.width && img.height ? `${img.width} x ${img.height}` : ''),
        detailBlock('Type', img.type || 'output'),
    ].join('');

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.getElementById('modalImage').src = '';
}

document.addEventListener('keydown', event => {
    if (event.key === 'Escape') closeImageModal();
});

function visibleImages() {
    const completed = galleryImages.filter(img => img.file_name);
    if (activeFolder === 'all') return completed;
    if (activeFolder === 'unfiled') return completed.filter(img => !img.gallery_folder_id);
    return completed.filter(img => Number(img.gallery_folder_id) === Number(activeFolder));
}

function renderFolders() {
    const allCount = galleryImages.filter(img => img.file_name).length;
    const unfiledCount = galleryImages.filter(img => img.file_name && !img.gallery_folder_id).length;
    document.getElementById('allCount').textContent = allCount;
    document.getElementById('unfiledCount').textContent = unfiledCount;

    document.querySelectorAll('.folder-filter').forEach(button => {
        const value = button.dataset.folderFilter;
        const active = String(activeFolder) === String(value);
        button.className = `folder-filter flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-left text-sm font-semibold transition ${active ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100'}`;
    });

    const folderList = document.getElementById('folderList');
    if (!galleryFolders.length) {
        folderList.innerHTML = '<div class="rounded-xl border border-dashed border-slate-300 px-3 py-4 text-sm text-slate-500">No folders yet.</div>';
        return;
    }

    folderList.innerHTML = galleryFolders.map(folder => {
        const count = galleryImages.filter(img => Number(img.gallery_folder_id) === Number(folder.id)).length;
        const active = String(activeFolder) === String(folder.id);
        return `
            <div class="flex items-center gap-2">
                <button type="button" data-folder-filter="${folder.id}"
                    class="folder-filter min-w-0 flex-1 rounded-xl px-3 py-2.5 text-left text-sm font-semibold transition ${active ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100'}">
                    <span class="block truncate">${escapeHtml(folder.name)}</span>
                    <span class="block text-xs ${active ? 'text-indigo-100' : 'text-slate-500'}">${escapeHtml(folder.user?.name || `User #${folder.user_id}`)} - ${count}</span>
                </button>
                <button type="button" onclick="deleteFolder(${folder.id}, '${escapeHtml(folder.name)}')"
                    class="rounded-xl border border-rose-200 px-2.5 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">
                    Delete
                </button>
            </div>
        `;
    }).join('');

    document.querySelectorAll('[data-folder-filter]').forEach(button => {
        button.addEventListener('click', () => {
            activeFolder = button.dataset.folderFilter;
            renderFolders();
            renderImages();
        });
    });
}

function folderOptions(image) {
    const options = ['<option value="">Unfiled</option>'].concat(
        compatibleFolders(image).map(folder => `<option value="${folder.id}" ${Number(image.gallery_folder_id) === Number(folder.id) ? 'selected' : ''}>${escapeHtml(folder.name)}</option>`)
    );
    return options.join('');
}

function renderImages() {
    const gallery = document.getElementById('images');
    const images = visibleImages();

    if (!images.length) {
        gallery.innerHTML = '<div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-14 text-center shadow-sm">No images found.</div>';
        return;
    }

    gallery.innerHTML = images.map(img => {
        const imageUrl = `/api/comfyui/view?filename=${encodeURIComponent(img.file_name)}&subfolder=${encodeURIComponent(img.subfolder || '')}&type=${encodeURIComponent(img.type || 'output')}`;
        const folder = galleryFolders.find(item => Number(item.id) === Number(img.gallery_folder_id));

        return `
            <article class="group overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                <div class="relative aspect-square overflow-hidden bg-slate-100">
                    <img src="${imageUrl}" alt="Generated image" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]" onclick="openImageModal(${img.id}, this.src)">
                </div>

                <div class="space-y-3 p-4">
                    <div>
                        <p class="truncate text-sm font-semibold text-slate-800">${escapeHtml(ownerLabel(img))}</p>
                        <p class="mt-1 text-xs text-slate-500">${escapeHtml(folder ? folder.name : 'Unfiled')} - ID ${escapeHtml(img.id)}</p>
                    </div>
                    <p class="line-clamp-2 text-xs leading-5 text-slate-500">${escapeHtml(img.positive_prompt || 'No prompt')}</p>

                    <label class="block text-xs font-semibold text-slate-600">
                        Move to folder
                        <select name="gallery_folder_id" onchange="moveImage(${img.id}, this.value)"
                            class="mt-1 w-full rounded-xl border-slate-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            ${folderOptions(img)}
                        </select>
                    </label>

                    <button type="button" onclick="deleteAdminImage(${img.id})"
                        class="w-full rounded-2xl bg-rose-500 px-3 py-2.5 text-xs font-semibold text-white transition hover:bg-rose-600">
                        Delete image
                    </button>
                </div>
            </article>
        `;
    }).join('');
}

async function loadFolders() {
    const response = await fetch('/admin/api/folders', { headers: { 'Accept': 'application/json' } });
    const folders = await response.json();
    if (!response.ok) throw new Error(folders.message || 'Failed to load folders');
    galleryFolders = folders || [];
}

async function loadImages() {
    const response = await fetch('/admin/api/images', { headers: { 'Accept': 'application/json' } });
    const images = await response.json();
    if (!response.ok) throw new Error(images.message || 'Failed to load images');
    galleryImages = images || [];
}

async function refreshAdminGallery() {
    const refreshBtn = document.getElementById('refreshGalleryBtn');
    refreshBtn.disabled = true;
    refreshBtn.textContent = 'Loading...';

    try {
        await Promise.all([loadFolders(), loadImages()]);
        renderFolders();
        renderImages();
        setGalleryStatus(`Loaded ${galleryImages.filter(img => img.file_name).length} image(s).`, 'success');
    } catch (error) {
        setGalleryStatus(`Gallery error: ${error.message}`, 'error');
    } finally {
        refreshBtn.disabled = false;
        refreshBtn.textContent = 'Refresh';
    }
}

async function createFolder(event) {
    event.preventDefault();
    const userId = document.getElementById('folderUserId').value;
    const nameInput = document.getElementById('folderName');
    const name = nameInput.value.trim();
    if (!userId || !name) return;

    try {
        const response = await fetch('/admin/api/folders', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ user_id: Number(userId), name })
        });
        const result = await response.json();
        if (!response.ok) throw new Error(result.message || 'Folder create failed');
        nameInput.value = '';
        activeFolder = String(result.id);
        await refreshAdminGallery();
        setGalleryStatus('Folder created.', 'success');
    } catch (error) {
        setGalleryStatus(`Folder error: ${error.message}`, 'error');
    }
}

async function deleteFolder(id, name) {
    if (!confirm(`Delete folder "${name}"? Images will move to Unfiled.`)) return;

    try {
        const response = await fetch(`/admin/api/folders/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' }
        });
        const result = await response.json();
        if (!response.ok) throw new Error(result.message || 'Folder delete failed');
        activeFolder = 'all';
        await refreshAdminGallery();
        setGalleryStatus('Folder deleted.', 'success');
    } catch (error) {
        setGalleryStatus(`Folder delete error: ${error.message}`, 'error');
    }
}

async function moveImage(id, folderId) {
    try {
        const response = await fetch(`/admin/gallery/${id}/folder`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ folder_id: folderId ? Number(folderId) : null })
        });
        const result = await response.json();
        if (!response.ok) throw new Error(result.message || 'Move failed');
        await refreshAdminGallery();
        setGalleryStatus('Image moved.', 'success');
    } catch (error) {
        setGalleryStatus(`Move error: ${error.message}`, 'error');
        refreshAdminGallery();
    }
}

async function deleteAdminImage(id) {
    if (!confirm('Delete this image?')) return;

    const response = await fetch(`/admin/gallery/delete/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' }
    });

    const result = await response.json();
    if (response.ok && result.success) refreshAdminGallery();
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('imageModal')?.addEventListener('click', event => {
        if (event.target === event.currentTarget) {
            closeImageModal();
        }
    });
    refreshAdminGallery();
    document.getElementById('refreshGalleryBtn')?.addEventListener('click', refreshAdminGallery);
    document.getElementById('folderForm')?.addEventListener('submit', createFolder);
    document.querySelectorAll('[data-folder-filter]').forEach(button => {
        button.addEventListener('click', () => {
            activeFolder = button.dataset.folderFilter;
            renderFolders();
            renderImages();
        });
    });
});
</script>
@endpush
@endsection
