@extends('errors.layout')

@section('title', '500 — Server Error')
@section('code',  '500')
@section('icon',  'fa-server')
@section('heading', 'Internal Server Error')
@section('desc',  'Something went wrong on our end. Our team has been notified. Please try again in a few moments.')

@push('styles')
<style>
    :root {
        --error-color:   #ef4444;
        --error-bg:      #fef2f2;
        --error-icon-bg: rgba(239,68,68,.12);
        --error-border:  #fca5a5;
        --error-badge:   #991b1b;
    }
</style>
@endpush

@section('actions')
        <a href="{{ url('/') }}" class="error-btn-primary">
        <i class="fas fa-arrow-left" style="font-size:.8rem;"></i>
        Go to Dashboard
    </a>

@endsection
