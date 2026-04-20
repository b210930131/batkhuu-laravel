
    <div>
        <div class="space-y-6">
            
                <x-slot name="heading">🖼️ Бүх хэрэглэгчдийн зургууд</x-slot>
                
                @if($this->images && $this->images->count() > 0)
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($this->images as $image)
                            <div class="border rounded-lg overflow-hidden bg-white">
                                @if($image->file_name)
                                    <img src="/outputs/{{ $image->file_name }}" 
                                         class="w-full h-40 object-cover"
                                         onclick="window.open(this.src)"
                                         style="cursor: pointer;">
                                @else
                                    <div class="w-full h-40 bg-gray-100 flex items-center justify-center">
                                        🎨 Generating...
                                    </div>
                                @endif
                                
                                <div class="p-2 text-xs">
                                    <div class="font-medium">{{ $image->user->name ?? 'Unknown' }}</div>
                                    <div class="text-gray-500 truncate">{{ $image->positive_prompt ?? '' }}</div>
                                </div>
                                
                                <!-- Устгах товч - доод хэсэгт -->
                                <div class="p-2 border-t bg-gray-50">
                                    <button 
                                        onclick="if(confirm('Энэ зургийг устгахдаа итгэлтэй байна уу?')) fetch('/admin/gallery/delete/{{ $image->id }}', {
                                            method: 'DELETE', 
                                            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                                        }).then(() => location.reload())"
                                        class="w-full px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition">
                                        🗑️ Устгах
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500">
                        🎨 Зураг байхгүй байна.
                    </div>
                @endif
            
        </div>
    </div>
