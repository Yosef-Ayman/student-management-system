@extends('errors.layout')

@section('title', '503 — Service Unavailable')
@section('code',  '503')
@section('icon',  'fa-tools')
@section('heading', 'Under Maintenance')
@section('desc',  'Centarica is currently undergoing scheduled maintenance. We will be back online shortly. Thank you for your patience.')

@push('styles')
<style>
    :root {
        --error-color:   #6366f1;
        --error-bg:      #eef2ff;
        --error-icon-bg: rgba(99,102,241,.12);
        --error-border:  #a5b4fc;
        --error-badge:   #3730a3;
    }
</style>
@endpush

@section('actions')
    <a href="{{ url()->current() }}" class="error-btn-primary">
        <i class="fas fa-arrow-left" style="font-size:.8rem;"></i>
        Refresh
    </a>

@endsection
