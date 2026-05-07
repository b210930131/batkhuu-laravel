@extends('imagegen.customer.layouts.app')

@section('title', 'AI Studio')
@section('page_title', 'AI Studio')
@section('page_subtitle', 'Create stylish AI-generated images with modern controls')

@section('content')
    @include('partials.generation-form')
@endsection