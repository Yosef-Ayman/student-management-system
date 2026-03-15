@extends('errors.layout')

@section('title', '419 — Page Expired')
@section('code',  '419')
@section('icon',  'fa-clock')
@section('heading', 'Session Expired')
@section('desc',  'Your session has expired due to inactivity or a form token mismatch. Please refresh and try again.')

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
    <a href="{{ url()->current() }}" class="error-btn-primary">
        <i class="fas fa-arrow-left" style="font-size:.8rem;"></i>
        Refresh Page
    </a>

@endsection
