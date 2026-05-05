@extends($layout)

@section('title', 'Blender Studio')
@section('page_title', 'Blender Studio')
@section('page_subtitle', 'Build a fixed room scene, preview the plan, and render a ControlNet input')

@section('content')
<div class="grid grid-cols-2 gap-5 2xl:grid-cols-[minmax(0,1fr)_520px]">
    <section class="space-y-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Room Builder</h3>
                    <p class="mt-1 text-sm text-slate-500">Fixed script controls for walls, openings, structure, and camera.</p>
                </div>
                <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">{{ ucfirst($panel) }}</span>
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                <label class="text-sm font-semibold text-slate-700">Width
                    <input id="roomWidth" type="number" min="2" max="20" step="0.1" value="5.2" class="mt-1 w-full rounded-xl border-slate-300 text-sm">
                </label>
                <label class="text-sm font-semibold text-slate-700">Length
                    <input id="roomLength" type="number" min="2" max="30" step="0.1" value="6.4" class="mt-1 w-full rounded-xl border-slate-300 text-sm">
                </label>
                <label class="text-sm font-semibold text-slate-700">Height
                    <input id="roomHeight" type="number" min="2" max="6" step="0.1" value="2.8" class="mt-1 w-full rounded-xl border-slate-300 text-sm">
                </label>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-base font-bold text-slate-900">Camera</h3>
                <div class="mt-3 grid grid-cols-2 gap-2">
                    <button type="button" onclick="applyCameraPreset('corner')" class="rounded-xl bg-indigo-600 px-3 py-2 text-xs font-semibold text-white">Corner wide</button>
                    <button type="button" onclick="applyCameraPreset('door')" class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white">Door view</button>
                    <button type="button" onclick="applyCameraPreset('window')" class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white">Window view</button>
                    <button type="button" onclick="applyCameraPreset('top')" class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white">Top angled</button>
                    <button type="button" onclick="resetCameraDefault()" class="rounded-xl bg-emerald-600 px-3 py-2 text-xs font-semibold text-white">Reset camera</button>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <label class="text-xs font-semibold text-slate-600">Camera X<input id="camX" type="range" min="-3" max="12" step="0.1" value="3.8" class="w-full"></label>
                    <label class="text-xs font-semibold text-slate-600">Camera Y<input id="camY" type="range" min="-3" max="14" step="0.1" value="1.1" class="w-full"></label>
                    <label class="text-xs font-semibold text-slate-600">Target X<input id="targetX" type="range" min="0" max="12" step="0.1" value="3.8" class="w-full"></label>
                    <label class="text-xs font-semibold text-slate-600">Target Y<input id="targetY" type="range" min="0" max="14" step="0.1" value="3.7" class="w-full"></label>
                    <label class="text-xs font-semibold text-slate-600">Height<input id="camHeight" type="range" min="0.8" max="3.5" step="0.1" value="1.5" class="w-full"></label>
                    <label class="text-xs font-semibold text-slate-600">FOV<input id="camFov" type="range" min="25" max="90" step="1" value="66" class="w-full"></label>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">Openings</h3>
                    <div class="flex gap-2">
                        <button type="button" onclick="addDoor()" class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white">Add door</button>
                        <button type="button" onclick="addWindow()" class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white">Add window</button>
                    </div>
                </div>
                <div id="openingsList" class="space-y-3"></div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">Structure</h3>
                    <div class="flex gap-2">
                        <button type="button" onclick="addColumn()" class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white">Column</button>
                        <button type="button" onclick="addBeam()" class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white">Beam</button>
                    </div>
                </div>
                <div id="columnsList" class="space-y-3"></div>
                <div id="beamsList" class="mt-3 space-y-3"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">Electrical</h3>
                    <button type="button" onclick="addElectrical()" class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white">Add</button>
                </div>
                <div id="electricalList" class="space-y-3"></div>
            </div>

            
        </div>
    </section>

    <aside class="space-y-4">
        <div class="sticky top-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">Room Plan</h3>
                <span id="planSize" class="text-xs font-semibold text-slate-500"></span>
            </div>
            <svg id="roomPlan" viewBox="0 0 520 520" class="h-auto w-full rounded-xl bg-slate-50 ring-1 ring-slate-200"></svg>
            <div class="mt-3 grid grid-cols-2 gap-2 text-xs text-slate-500">
                <div><span class="inline-block h-2.5 w-2.5 rounded-sm bg-rose-500"></span> door</div>
                <div><span class="inline-block h-2.5 w-2.5 rounded-sm bg-blue-500"></span> window</div>
                <div><span class="inline-block h-2.5 w-2.5 rounded-sm bg-teal-500"></span> balcony</div>
                <div><span class="inline-block h-2.5 w-2.5 rounded-sm bg-slate-500"></span> column/beam</div>
            </div>
            <button type="button" onclick="openCameraModal(event)" class="mt-3 w-full rounded-xl bg-slate-900 px-3 py-2 text-xs font-bold text-white">
                Camera settings
            </button>
            <p class="mt-3 text-xs leading-5 text-slate-500">Click objects to edit. Drag the green camera point or black target point to change view.</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <button id="renderBtn" type="button" onclick="renderBlender()" class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-indigo-700">
                Render Blender Input
            </button>
            <div id="renderStatus" class="mt-3 hidden rounded-xl border px-4 py-3 text-sm font-semibold"></div>
            <div id="renderResult" class="mt-4"></div>
        </div>
    </aside>
</div>

<div id="objectModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 p-4 backdrop-blur-sm">
    <div class="mx-auto mt-16 max-w-xl rounded-2xl bg-white p-5 shadow-2xl">
        <div class="mb-4 flex items-start justify-between gap-3">
            <div>
                <h3 id="objectModalTitle" class="text-lg font-bold text-slate-900">Edit object</h3>
                <p id="objectModalHint" class="mt-1 text-sm text-slate-500"></p>
            </div>
            <button type="button" onclick="closeObjectModal()" class="rounded-xl border border-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-600">Close</button>
        </div>
        <div id="objectModalFields" class="grid grid-cols-2 gap-3"></div>
        <div class="mt-5 flex justify-between gap-3">
            <button type="button" onclick="deleteActiveObject()" class="rounded-xl bg-rose-500 px-4 py-2 text-sm font-bold text-white">Delete</button>
            <button type="button" onclick="closeObjectModal()" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-bold text-white">Done</button>
        </div>
    </div>
</div>

<div id="cameraModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 p-4 backdrop-blur-sm">
    <div class="mx-auto mt-12 max-w-2xl rounded-2xl bg-white p-5 shadow-2xl">
        <div class="mb-4 flex items-start justify-between gap-3">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Camera Control</h3>
                <p class="mt-1 text-sm text-slate-500">Drag the camera on the plan, or tune position and viewing angle here.</p>
            </div>
            <button type="button" onclick="closeCameraModal()" class="rounded-xl border border-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-600">Close</button>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <label class="text-xs font-semibold text-slate-600">Camera X<input id="modalCamX" type="number" step="0.1" class="mt-1 w-full rounded-xl border-slate-300 text-sm"></label>
            <label class="text-xs font-semibold text-slate-600">Camera Y<input id="modalCamY" type="number" step="0.1" class="mt-1 w-full rounded-xl border-slate-300 text-sm"></label>
            <label class="text-xs font-semibold text-slate-600">Target X<input id="modalTargetX" type="number" step="0.1" class="mt-1 w-full rounded-xl border-slate-300 text-sm"></label>
            <label class="text-xs font-semibold text-slate-600">Target Y<input id="modalTargetY" type="number" step="0.1" class="mt-1 w-full rounded-xl border-slate-300 text-sm"></label>
            <label class="text-xs font-semibold text-slate-600">Camera height<input id="modalCamHeight" type="number" step="0.1" class="mt-1 w-full rounded-xl border-slate-300 text-sm"></label>
            <label class="text-xs font-semibold text-slate-600">FOV / wide angle<input id="modalCamFov" type="number" step="1" class="mt-1 w-full rounded-xl border-slate-300 text-sm"></label>
        </div>

        <div class="mt-4 grid grid-cols-3 gap-2">
            <button type="button" onclick="orbitCameraTarget(-8)" class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-bold text-white">Angle left</button>
            <button type="button" onclick="moveCameraForward(0.25)" class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-bold text-white">Forward</button>
            <button type="button" onclick="orbitCameraTarget(8)" class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-bold text-white">Angle right</button>
            <button type="button" onclick="strafeCamera(-0.25)" class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-bold text-white">Move left</button>
            <button type="button" onclick="moveCameraForward(-0.25)" class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-bold text-white">Back</button>
            <button type="button" onclick="strafeCamera(0.25)" class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-bold text-white">Move right</button>
        </div>

        <div class="mt-5 flex justify-end">
            <button type="button" onclick="closeCameraModal()" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-bold text-white">Done</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
const state = {
    openings: [
        { type: 'door', wall: 'north', position: 1.45, width: 1.18, height: 2.2, sill: 0 },
        { type: 'window', wall: 'north', position: 3.05, width: 1.9, height: 1.45, sill: 0.62 },
    ],
    columns: [{ x: 5, y: 6.0, width: 0.35, depth: 0.35 }],
    beams: [{ direction: 'x', position: 6, width: 0.35, depth: 0.32 }],
    electrical: [
        { type: 'switch', wall: 'north', position: 1, height: 1.1 },
        { type: 'socket', wall: 'west', position: 2.6, height: 0.35 },
    ],
    cameraPreset: 'corner',
    activeObject: null,
    draggingPoint: null,
    dragMoved: false,
};

function room() {
    return {
        width: Number(document.getElementById('roomWidth').value),
        length: Number(document.getElementById('roomLength').value),
        height: Number(document.getElementById('roomHeight').value),
    };
}

function field(value, onInput, type = 'number', options = []) {
    const handler = onInput.replace('__VALUE__', 'this.value');
    if (type === 'select') {
        return `<select onchange="${handler}" class="mt-1 w-full rounded-lg border-slate-300 text-xs">${options.map(option => `<option value="${option}" ${value === option ? 'selected' : ''}>${option}</option>`).join('')}</select>`;
    }
    return `<input type="number" step="0.1" value="${value}" oninput="${handler}" class="mt-1 w-full rounded-lg border-slate-300 text-xs">`;
}

function labeledField(label, value, onInput, type = 'number', options = []) {
    return `<label class="block text-[11px] font-semibold text-slate-600">${label}${field(value, onInput, type, options)}</label>`;
}

function renderLists() {
    document.getElementById('openingsList').innerHTML = state.openings.map((item, i) => `
        <div class="rounded-xl bg-slate-50 p-3">
            <div class="mb-2 flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-slate-500">Opening ${i + 1}</span>
                <button type="button" onclick="removeItem('openings', ${i})" class="text-xs font-bold text-rose-600">Delete</button>
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs">
                ${labeledField('Type', item.type, `updateOpening(${i}, 'type', __VALUE__)`, 'select', ['door', 'window', 'balcony'])}
                ${labeledField('Wall', item.wall, `updateOpening(${i}, 'wall', __VALUE__)`, 'select', ['north', 'south', 'east', 'west'])}
                ${labeledField('Position from wall left', item.position, `updateOpening(${i}, 'position', __VALUE__)`)}
                ${labeledField('Width', item.width, `updateOpening(${i}, 'width', __VALUE__)`)}
                ${labeledField('Height', item.height, `updateOpening(${i}, 'height', __VALUE__)`)}
                ${labeledField('Sill height', item.sill, `updateOpening(${i}, 'sill', __VALUE__)`)}
            </div>
        </div>`).join('');

    document.getElementById('columnsList').innerHTML = state.columns.map((item, i) => `
        <div class="rounded-xl bg-slate-50 p-3 text-xs">
            <div class="mb-2 flex justify-between"><b>Column</b><button type="button" onclick="removeItem('columns', ${i})" class="font-bold text-rose-600">Delete</button></div>
            <div class="grid grid-cols-2 gap-2">
                ${labeledField('X position', item.x, `updateArray('columns', ${i}, 'x', __VALUE__)`)}
                ${labeledField('Y position', item.y, `updateArray('columns', ${i}, 'y', __VALUE__)`)}
                ${labeledField('Width', item.width, `updateArray('columns', ${i}, 'width', __VALUE__)`)}
                ${labeledField('Depth', item.depth, `updateArray('columns', ${i}, 'depth', __VALUE__)`)}
            </div>
        </div>`).join('');

    document.getElementById('beamsList').innerHTML = state.beams.map((item, i) => `
        <div class="rounded-xl bg-slate-50 p-3 text-xs">
            <div class="mb-2 flex justify-between"><b>Beam</b><button type="button" onclick="removeItem('beams', ${i})" class="font-bold text-rose-600">Delete</button></div>
            <div class="grid grid-cols-2 gap-2">
                ${labeledField('Direction', item.direction, `updateArray('beams', ${i}, 'direction', __VALUE__)`, 'select', ['x', 'y'])}
                ${labeledField('Position', item.position, `updateArray('beams', ${i}, 'position', __VALUE__)`)}
                ${labeledField('Width', item.width, `updateArray('beams', ${i}, 'width', __VALUE__)`)}
                ${labeledField('Depth', item.depth, `updateArray('beams', ${i}, 'depth', __VALUE__)`)}
            </div>
        </div>`).join('');

    document.getElementById('electricalList').innerHTML = state.electrical.map((item, i) => `
        <div class="rounded-xl bg-slate-50 p-3 text-xs">
            <div class="mb-2 flex justify-between">
                <span class="font-bold uppercase text-slate-500">Electrical ${i + 1}</span>
                <button type="button" onclick="removeItem('electrical', ${i})" class="font-bold text-rose-600">Delete</button>
            </div>
            <div class="grid grid-cols-2 gap-2">
                ${labeledField('Type', item.type, `updateArray('electrical', ${i}, 'type', __VALUE__)`, 'select', ['switch', 'socket'])}
                ${labeledField('Wall', item.wall, `updateArray('electrical', ${i}, 'wall', __VALUE__)`, 'select', ['north', 'south', 'east', 'west'])}
                ${labeledField('Position from wall left', item.position, `updateArray('electrical', ${i}, 'position', __VALUE__)`)}
                ${labeledField('Height from floor', item.height, `updateArray('electrical', ${i}, 'height', __VALUE__)`)}
            </div>
        </div>`).join('');
}

function updateOpening(index, key, value) { updateArray('openings', index, key, value); }
function updateArray(group, index, key, value) {
    state[group][index][key] = ['type', 'wall', 'direction'].includes(key) ? value : Number(value);
    drawPlan();
}
function removeItem(group, index) { state[group].splice(index, 1); renderLists(); drawPlan(); }
function addDoor() { state.openings.push({ type: 'door', wall: 'east', position: 0.35, width: 1.18, height: 2.2, sill: 0 }); renderLists(); drawPlan(); }
function addWindow() { state.openings.push({ type: 'window', wall: 'east', position: 3.05, width: 1.9, height: 1.45, sill: 0.62 }); renderLists(); drawPlan(); }
function addOpening() { addWindow(); }
function addColumn() { state.columns.push({ x: 2, y: 2, width: 0.3, depth: 0.3 }); renderLists(); drawPlan(); }
function addBeam() { state.beams.push({ direction: 'x', position: 2, width: 0.25, depth: 0.3 }); renderLists(); drawPlan(); }
function addElectrical() { state.electrical.push({ type: 'switch', wall: 'south', position: 1, height: 1.1 }); renderLists(); drawPlan(); }

function scalePoint(x, y, r, pad, scale) { return { x: pad + x * scale, y: pad + (r.length - y) * scale }; }
function wallPoint(wall, pos, r, pad, scale) {
    if (wall === 'north') return scalePoint(pos, r.length, r, pad, scale);
    if (wall === 'south') return scalePoint(pos, 0, r, pad, scale);
    if (wall === 'east') return scalePoint(r.width, pos, r, pad, scale);
    return scalePoint(0, pos, r, pad, scale);
}

function drawPlan() {
    const svg = document.getElementById('roomPlan');
    const r = room();
    const pad = 42;
    const scale = Math.min(430 / r.width, 430 / r.length);
    const start = scalePoint(0, r.length, r, pad, scale);
    const size = { w: r.width * scale, h: r.length * scale };
    const cam = camera();
    document.getElementById('planSize').textContent = `${r.width}m x ${r.length}m`;

    let html = `<rect x="${start.x}" y="${start.y}" width="${size.w}" height="${size.h}" fill="#fff" stroke="#0f172a" stroke-width="5"/>`;
    state.openings.forEach((item, index) => {
        const p = wallPoint(item.wall, item.position, r, pad, scale);
        const len = item.width * scale;
        const color = item.type === 'door' ? '#ef4444' : item.type === 'balcony' ? '#14b8a6' : '#3b82f6';
        if (['north', 'south'].includes(item.wall)) {
            html += `<rect onclick="openObjectModal(event, 'openings', ${index})" class="cursor-pointer" x="${p.x}" y="${p.y - 7}" width="${len}" height="14" fill="${color}" rx="3"/>`;
        } else {
            html += `<rect onclick="openObjectModal(event, 'openings', ${index})" class="cursor-pointer" x="${p.x - 7}" y="${p.y - len}" width="14" height="${len}" fill="${color}" rx="3"/>`;
        }
    });
    state.columns.forEach((col, index) => {
        const p = scalePoint(col.x - col.width / 2, col.y + col.depth / 2, r, pad, scale);
        html += `<rect onclick="openObjectModal(event, 'columns', ${index})" class="cursor-pointer" x="${p.x}" y="${p.y}" width="${col.width * scale}" height="${col.depth * scale}" fill="#64748b" rx="2"/>`;
    });
    state.beams.forEach((beam, index) => {
        if (beam.direction === 'x') {
            const p = scalePoint(0, beam.position + beam.width / 2, r, pad, scale);
            html += `<rect onclick="openObjectModal(event, 'beams', ${index})" class="cursor-pointer" x="${p.x}" y="${p.y}" width="${r.width * scale}" height="${beam.width * scale}" fill="#94a3b8" opacity="0.55"/>`;
        } else {
            const p = scalePoint(beam.position - beam.width / 2, r.length, r, pad, scale);
            html += `<rect onclick="openObjectModal(event, 'beams', ${index})" class="cursor-pointer" x="${p.x}" y="${p.y}" width="${beam.width * scale}" height="${r.length * scale}" fill="#94a3b8" opacity="0.55"/>`;
        }
    });
    state.electrical.forEach((item, index) => {
        const p = wallPoint(item.wall, item.position, r, pad, scale);
        html += `<circle onclick="openObjectModal(event, 'electrical', ${index})" class="cursor-pointer" cx="${p.x}" cy="${p.y}" r="7" fill="${item.type === 'switch' ? '#f59e0b' : '#8b5cf6'}"/>`;
    });
    const cp = scalePoint(cam.x, cam.y, r, pad, scale);
    const tp = scalePoint(cam.target_x, cam.target_y, r, pad, scale);
    html += `<line x1="${cp.x}" y1="${cp.y}" x2="${tp.x}" y2="${tp.y}" stroke="#16a34a" stroke-width="2" stroke-dasharray="5 5"/>`;
    html += `<circle onmousedown="startCameraDrag(event, 'camera')" onmouseup="finishCameraClick(event)" class="cursor-move" cx="${cp.x}" cy="${cp.y}" r="10" fill="#16a34a"/>`;
    html += `<circle onmousedown="startCameraDrag(event, 'target')" class="cursor-crosshair" cx="${tp.x}" cy="${tp.y}" r="7" fill="#0f172a"/>`;
    svg.innerHTML = html;
}

function camera() {
    return {
        preset: state.cameraPreset,
        x: Number(document.getElementById('camX').value),
        y: Number(document.getElementById('camY').value),
        target_x: Number(document.getElementById('targetX').value),
        target_y: Number(document.getElementById('targetY').value),
        height: Number(document.getElementById('camHeight').value),
        fov: Number(document.getElementById('camFov').value),
    };
}

function applyCameraPreset(name) {
    const r = room();
    state.cameraPreset = name;
    const presets = {
        reset: [3.8, 1.1, 3.8, 3.7, 1.5, 66],
        corner: [r.width * 0.82, -1.8, r.width * 0.36, r.length * 0.56, 1.55, 72],
        door: [r.width * 0.55, -2.2, r.width * 0.56, r.length * 0.62, 1.45, 68],
        window: [r.width * 0.78, -1.6, r.width * 0.2, r.length * 0.58, 1.5, 66],
        top: [r.width * 0.72, -1.8, r.width * 0.5, r.length * 0.5, 3.2, 76],
    }[name];
    ['camX','camY','targetX','targetY','camHeight','camFov'].forEach((id, i) => document.getElementById(id).value = presets[i]);
    drawPlan();
}

function resetCameraDefault() {
    applyCameraPreset('reset');
}

function openObjectModal(event, group, index) {
    event.stopPropagation();
    state.activeObject = { group, index };
    const labels = { openings: 'Opening', columns: 'Column', beams: 'Beam', electrical: 'Electrical' };
    const item = state[group][index];
    document.getElementById('objectModalTitle').textContent = `${labels[group]} ${index + 1}`;
    document.getElementById('objectModalHint').textContent = group === 'openings'
        ? 'Wall position is measured from the left side of the selected wall.'
        : 'Values are in meters inside the room plan.';
    document.getElementById('objectModalFields').innerHTML = objectFields(group, index, item);
    document.getElementById('objectModal').classList.remove('hidden');
}

function objectFields(group, index, item) {
    const map = {
        openings: [
            ['type', 'Type', 'select', ['door', 'window', 'balcony']],
            ['wall', 'Wall', 'select', ['north', 'south', 'east', 'west']],
            ['position', 'Position from wall left'],
            ['width', 'Width'],
            ['height', 'Height'],
            ['sill', 'Sill height'],
        ],
        columns: [['x', 'X position'], ['y', 'Y position'], ['width', 'Width'], ['depth', 'Depth']],
        beams: [
            ['direction', 'Direction', 'select', ['x', 'y']],
            ['position', 'Position'],
            ['width', 'Width'],
            ['depth', 'Depth'],
        ],
        electrical: [
            ['type', 'Type', 'select', ['switch', 'socket']],
            ['wall', 'Wall', 'select', ['north', 'south', 'east', 'west']],
            ['position', 'Position from wall left'],
            ['height', 'Height from floor'],
        ],
    };

    return map[group].map(([key, label, type, options]) => {
        return labeledField(label, item[key], `updateModalObject('${group}', ${index}, '${key}', __VALUE__)`, type || 'number', options || []);
    }).join('');
}

function updateModalObject(group, index, key, value) {
    updateArray(group, index, key, value);
    renderLists();
}

function deleteActiveObject() {
    if (!state.activeObject) return;
    removeItem(state.activeObject.group, state.activeObject.index);
    closeObjectModal();
}

function closeObjectModal() {
    state.activeObject = null;
    document.getElementById('objectModal').classList.add('hidden');
}

function setControlValue(id, value) {
    document.getElementById(id).value = Number(value).toFixed(1);
}

function syncCameraModal() {
    const cam = camera();
    document.getElementById('modalCamX').value = cam.x.toFixed(1);
    document.getElementById('modalCamY').value = cam.y.toFixed(1);
    document.getElementById('modalTargetX').value = cam.target_x.toFixed(1);
    document.getElementById('modalTargetY').value = cam.target_y.toFixed(1);
    document.getElementById('modalCamHeight').value = cam.height.toFixed(1);
    document.getElementById('modalCamFov').value = cam.fov.toFixed(0);
}

function applyCameraModalValues() {
    setControlValue('camX', document.getElementById('modalCamX').value);
    setControlValue('camY', document.getElementById('modalCamY').value);
    setControlValue('targetX', document.getElementById('modalTargetX').value);
    setControlValue('targetY', document.getElementById('modalTargetY').value);
    setControlValue('camHeight', document.getElementById('modalCamHeight').value);
    document.getElementById('camFov').value = Number(document.getElementById('modalCamFov').value).toFixed(0);
    drawPlan();
}

function openCameraModal(event) {
    event.stopPropagation();
    syncCameraModal();
    document.getElementById('cameraModal').classList.remove('hidden');
}

function closeCameraModal() {
    document.getElementById('cameraModal').classList.add('hidden');
}

function orbitCameraTarget(degrees) {
    const cam = camera();
    const dx = cam.target_x - cam.x;
    const dy = cam.target_y - cam.y;
    const angle = Math.atan2(dy, dx) + degrees * Math.PI / 180;
    const distance = Math.max(0.5, Math.sqrt(dx * dx + dy * dy));
    setControlValue('targetX', cam.x + Math.cos(angle) * distance);
    setControlValue('targetY', cam.y + Math.sin(angle) * distance);
    syncCameraModal();
    drawPlan();
}

function strafeCamera(amount) {
    const cam = camera();
    const dx = cam.target_x - cam.x;
    const dy = cam.target_y - cam.y;
    const length = Math.max(0.01, Math.sqrt(dx * dx + dy * dy));
    const px = -dy / length * amount;
    const py = dx / length * amount;
    setControlValue('camX', cam.x + px);
    setControlValue('camY', cam.y + py);
    setControlValue('targetX', cam.target_x + px);
    setControlValue('targetY', cam.target_y + py);
    syncCameraModal();
    drawPlan();
}

function moveCameraForward(amount) {
    const cam = camera();
    const dx = cam.target_x - cam.x;
    const dy = cam.target_y - cam.y;
    const length = Math.max(0.01, Math.sqrt(dx * dx + dy * dy));
    const mx = dx / length * amount;
    const my = dy / length * amount;
    setControlValue('camX', cam.x + mx);
    setControlValue('camY', cam.y + my);
    setControlValue('targetX', cam.target_x + mx);
    setControlValue('targetY', cam.target_y + my);
    syncCameraModal();
    drawPlan();
}

function eventToRoomPoint(event) {
    const r = room();
    const rect = document.getElementById('roomPlan').getBoundingClientRect();
    const pad = 42;
    const scale = Math.min(430 / r.width, 430 / r.length);
    return {
        x: Math.max(-3, Math.min(r.width + 3, (event.clientX - rect.left - pad) / scale)),
        y: Math.max(-3, Math.min(r.length + 3, r.length - (event.clientY - rect.top - pad) / scale)),
    };
}

function startCameraDrag(event, point) {
    event.stopPropagation();
    state.draggingPoint = point;
    state.dragMoved = false;
}

function updateDraggedCamera(event) {
    if (!state.draggingPoint) return;
    state.dragMoved = true;
    const p = eventToRoomPoint(event);
    if (state.draggingPoint === 'camera') {
        setControlValue('camX', p.x);
        setControlValue('camY', p.y);
    } else {
        setControlValue('targetX', p.x);
        setControlValue('targetY', p.y);
    }
    drawPlan();
}

function stopCameraDrag() {
    state.draggingPoint = null;
}

function finishCameraClick(event) {
    event.stopPropagation();
    if (!state.dragMoved) {
        openCameraModal(event);
    }
    stopCameraDrag();
}

function payload() {
    return { room: room(), openings: state.openings, columns: state.columns, beams: state.beams, electrical: state.electrical, camera: camera() };
}

function setRenderStatus(message, type = 'info') {
    const el = document.getElementById('renderStatus');
    const styles = {
        info: 'border-indigo-200 bg-indigo-50 text-indigo-700',
        success: 'border-emerald-200 bg-emerald-50 text-emerald-700',
        error: 'border-rose-200 bg-rose-50 text-rose-700',
    };
    el.className = `mt-3 rounded-xl border px-4 py-3 text-sm font-semibold ${styles[type] || styles.info}`;
    el.textContent = message;
    el.classList.remove('hidden');
}

async function renderBlender() {
    const btn = document.getElementById('renderBtn');
    btn.disabled = true;
    btn.textContent = 'Rendering...';
    setRenderStatus('Starting fixed Blender script...', 'info');
    try {
        const response = await fetch('/api/blender/render', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify(payload()),
        });
        const data = await response.json();
        if (!response.ok || !data.success) throw new Error(data.error || data.message || 'Render failed');
        document.getElementById('renderResult').innerHTML = `<img src="${data.image.url}" class="w-full rounded-xl border border-slate-200">`;
        setRenderStatus('Render saved to Input Images.', 'success');
    } catch (error) {
        setRenderStatus(error.message, 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Render Blender Input';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    renderLists();
    resetCameraDefault();
    ['roomWidth','roomLength','roomHeight','camX','camY','targetX','targetY','camHeight','camFov'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', drawPlan);
    });
    document.getElementById('objectModal')?.addEventListener('click', event => {
        if (event.target === event.currentTarget) closeObjectModal();
    });
    document.getElementById('cameraModal')?.addEventListener('click', event => {
        if (event.target === event.currentTarget) closeCameraModal();
    });
    ['modalCamX','modalCamY','modalTargetX','modalTargetY','modalCamHeight','modalCamFov'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', applyCameraModalValues);
    });
    window.addEventListener('mousemove', updateDraggedCamera);
    window.addEventListener('mouseup', stopCameraDrag);
});
</script>
@endpush
@endsection
