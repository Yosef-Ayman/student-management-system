@extends('errors.layout')

@section('title', '404 — Not Found')
@section('code',  '404')
@section('icon',  'fa-search')
@section('heading', 'Page Not Found')
@section('desc',  'The page you are looking for doesn\'t exist or has been moved to a different location.')

@push('styles')
<style>
    :root {
        --error-color:   #3b82f6;
        --error-bg:      #eff6ff;
        --error-icon-bg: rgba(59,130,246,.12);
        --error-border:  #93c5fd;
        --error-badge:   #1e40af;
    }
</style>
@endpush

@section('actions')
    <a href="{{ url('/') }}" class="error-btn-primary">
        <i class="fas fa-arrow-left" style="font-size:.8rem;"></i>
        Go to Dashboard
    </a>
    <a href="{{ url('/') }}" class="error-btn-secondary">
        <i class="fas fa-home" style="font-size:.8rem;"></i>
        Home
    </a>
@endsection
