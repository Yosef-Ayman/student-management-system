@extends('errors.layout')

@section('title', '403 — Forbidden')
@section('code',  '403')
@section('icon',  'fa-ban')
@section('heading', 'Access Forbidden')
@section('desc',  'You don\'t have permission to access this page. Contact your administrator if you believe this is a mistake.')

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
    <a href="{{ url('/') }}" class="error-btn-secondary">
        <i class="fas fa-home" style="font-size:.8rem;"></i>
        Home
    </a>
@endsection
