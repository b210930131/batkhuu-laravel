@extends($dashboardLayout ?? 'layouts.app')

@section('title', $dashboardTitle ?? 'AI Dashboard')
@section('page_title', $dashboardHeading ?? 'Онлайн Платформ')
@section('page_subtitle', $dashboardSubtitle ?? 'AI dashboard')

@section('content')
    <div class="max-w-7xl mx-auto">
        @include('partials.index')
    </div>
@endsection
