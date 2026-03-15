@extends('errors.layout')

@section('title', '429 — Too Many Requests')
@section('code',  '429')
@section('icon',  'fa-exclamation-triangle')
@section('heading', 'Too Many Requests')
@section('desc',  'You have made too many requests in a short period of time. Please wait a moment before trying again.')

@push('styles')
<style>
    :root {
        --error-color:   #f97316;
        --error-bg:      #fff7ed;
        --error-icon-bg: rgba(249,115,22,.12);
        --error-border:  #fdba74;
        --error-badge:   #7c2d12;
    }
</style>
@endpush

@section('actions')
    <a href="{{ url()->previous() }}" class="error-btn-primary">
        <i class="fas fa-arrow-left" style="font-size:.8rem;"></i>
        Go Back
    </a>

@endsection
