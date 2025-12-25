@extends('layouts.front-layout')

@section('content')
    <div class="py-12">
        <div class="container">
            @if (isset($header))
                <div class="mb-4">
                    {{ $header }}
                </div>
            @endif

            {{ $slot }}
        </div>
    </div>
@endsection
