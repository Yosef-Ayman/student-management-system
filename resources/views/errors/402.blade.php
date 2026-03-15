@extends('errors.layout')

@section('title', '402 — Payment Required')
@section('code',  '402')
@section('icon',  'fa-credit-card')
@section('heading', 'Payment Required')
@section('desc',  'This feature requires an active subscription. Please upgrade your plan to continue.')

@push('styles')
<style>
    :root {
        --error-color:   #8b5cf6;
        --error-bg:      #f5f3ff;
        --error-icon-bg: rgba(139,92,246,.12);
        --error-border:  #c4b5fd;
        --error-badge:   #5b21b6;
    }
</style>
@endpush

@section('actions')
    <a href="{{ url()->previous() }}" class="error-btn-primary">
        <i class="fas fa-arrow-left" style="font-size:.8rem;"></i>
        Go Back
    </a>

@endsection
