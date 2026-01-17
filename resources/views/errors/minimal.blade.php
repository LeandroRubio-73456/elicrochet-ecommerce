@extends('layouts.front-layout')

@section('title')
    @yield('title') - EliCrochet
@endsection

@section('content')
<div class="container d-flex flex-column align-items-center justify-content-center py-5 my-5" style="min-height: 60vh;">
    <div class="text-center">
        <!-- Error Code -->
        <h1 class="display-1 fw-bold mb-0" style="color: var(--color-primary, #C16244); font-size: 6rem; line-height: 1;">
            @yield('code')
        </h1>

        <!-- Error Title -->
        <h2 class="h4 text-uppercase letter-spacing-2 mb-3 mt-2" style="color: var(--color-text, #1E1E1E); font-weight: 600;">
            @yield('title')
        </h2>

        <!-- Error Message -->
        <p class="lead mb-4 mx-auto" style="color: var(--color-text-light, #766352); max-width: 500px;">
            @yield('message')
        </p>

        <!-- Actions -->
        <div class="d-flex gap-3 justify-content-center">
            <a href="{{ url('/') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-medium" style="background-color: var(--color-text, #1E1E1E); border-color: var(--color-text, #1E1E1E);">
                Volver al Inicio
            </a>
            <a href="{{ route('contact') }}" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-medium" style="border-color: var(--color-primary, #C16244); color: var(--color-primary, #C16244);">
                Contáctanos
            </a>
        </div>
    </div>
</div>

<style>
    /* Inline styles for specific error page adjustments if needed */
    .letter-spacing-2 {
        letter-spacing: 2px;
    }
    .btn-primary:hover {
        background-color: var(--color-primary, #C16244) !important;
        border-color: var(--color-primary, #C16244) !important;
    }
    .btn-outline-primary:hover {
        background-color: var(--color-primary, #C16244) !important;
        color: white !important;
    }
</style>
@endsection
