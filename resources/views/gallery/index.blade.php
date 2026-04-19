@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white">🎨 My Private Gallery</h1>
        <a href="{{ route('home') }}" class="btn btn-outline-primary btn-sm">Back to Studio</a>
    </div>
    
    <div class="row">
        @forelse($images as $image)
            <div class="col-md-3 mb-4">
                <div class="card bg-dark border-secondary h-100 shadow-sm hover-shadow transition">
                    
                    {{-- Image Display Logic --}}
                    @if($image->file_name)
                        <img src="/api/comfyui/view?filename={{ urlencode($image->file_name) }}" 
                             class="card-img-top p-2" 
                             style="border-radius: 15px; height: 250px; object-fit: cover;"
                             onclick="window.open(this.src, '_blank')"
                             alt="Generated artwork">
                    @else
                        {{-- Loading/Placeholder State --}}
                        <div class="d-flex flex-column align-items-center justify-content-center bg-secondary text-white m-2" 
                             style="height: 250px; border-radius: 10px; opacity: 0.6;">
                            <div class="spinner-border spinner-border-sm mb-2" role="status"></div>
                            <span class="small text-uppercase">Painting...</span>
                        </div>
                    @endif
                    
                    <div class="card-body text-white d-flex flex-column">
                        <p class="small text-truncate flex-grow-1" title="{{ $image->positive_prompt }}">
                            {{ $image->positive_prompt }}
                        </p>
                        
                        <hr class="border-secondary my-2">
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex flex-column">
                                <small class="text-muted" style="font-size: 0.7rem;">
                                    {{ $image->created_at->diffForHumans() }}
                                </small>
                                <span class="badge bg-dark border border-primary text-primary mt-1" style="font-size: 0.6rem;">
                                    {{ Str::limit($image->model_used, 15) }}
                                </span>
                            </div>
                            
                            @if($image->file_name)
                                <a href="/api/comfyui/view?filename={{ urlencode($image->file_name) }}" 
                                   download="{{ $image->file_name }}" 
                                   class="btn btn-sm btn-link text-info p-0">
                                    📥
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center text-muted py-5">
                <div class="mb-3" style="font-size: 3rem;">🏜️</div>
                <h3>Your gallery is empty</h3>
                <p>Start your first generation in the studio!</p>
                <a href="{{ route('home') }}" class="btn btn-primary px-4">Go to Studio</a>
            </div>
        @endforelse
    </div>
    
    {{-- Pagination --}}
    <div class="mt-4 d-flex justify-content-center">
        {{ $images->links() }}
    </div>
</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.5) !important;
        transition: all 0.3s ease;
    }
    .transition { transition: all 0.3s ease; }
    .card-img-top { cursor: zoom-in; }
</style>
@endsection