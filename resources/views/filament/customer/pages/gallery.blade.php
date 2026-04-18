<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::card>
            <x-slot name="heading">🖼️ Миний зургууд</x-slot>
            
            @if($this->images && $this->images->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($this->images as $image)
                        <div class="border rounded-lg overflow-hidden">
                            @if($image->file_name)
                                <img src="/outputs/{{ $image->file_name }}" 
                                     class="w-full h-40 object-cover cursor-pointer"
                                     onclick="window.open(this.src)">
                            @else
                                <div class="w-full h-40 bg-gray-100 flex items-center justify-center">
                                    🎨 Generating...
                                </div>
                            @endif
                            <div class="p-2 text-xs text-gray-600 truncate">
                                {{ $image->positive_prompt ?? 'No prompt' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    🎨 Зураг байхгүй байна.<br>
                    <small>ComfyUI дээр зураг үүсгэж эхлээрэй.</small>
                </div>
            @endif
        </x-filament::card>
    </div>
</x-filament-panels::page>