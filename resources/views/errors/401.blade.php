@extends('errors.layout')

@section('title', '401 — Unauthorized')
@section('code',  '401')
@section('icon',  'fa-lock')
@section('heading', 'Access Denied')
@section('desc',  'You need to be logged in to access this page. Please sign in to continue.')

@push('styles')
<style>
    :root {
        --error-color:   #f59e0b;
        --error-bg:      #fffbeb;
        --error-icon-bg: rgba(245,158,11,.12);
        --error-border:  #fcd34d;
        --error-badge:   #92400e;
    }
</style>
@endpush

@section('actions')
    <a href="{{ route('login') }}" class="error-btn-primary">
        <i class="fas fa-arrow-left" style="font-size:.8rem;"></i>
        Go to Login
    </a>
    <a href="{{ url('/') }}" class="error-btn-secondary">
        <i class="fas fa-home" style="font-size:.8rem;"></i>
        Home
    </a>
@endsection
