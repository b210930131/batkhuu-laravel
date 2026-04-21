@extends('imagegen.customer.layouts.app')

@section('title', 'Generate Images')
@section('page_title', 'Generate Images')
@section('page_subtitle', 'Create stylish AI-generated images with modern controls')

@section('content')
    @include('partials.generation-form')
@endsection