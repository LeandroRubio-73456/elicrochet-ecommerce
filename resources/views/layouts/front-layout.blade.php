
<!DOCTYPE html>
<html lang="es">
<head>
    <title>@yield('title', 'EliCrochet')</title>
    @include('layouts.head-page-meta')
    @include('layouts.front-head-css')
    @stack('css')
</head>
<body class="landing-page d-flex flex-column min-vh-100">

    @include('front.partials.navbar')

    <main class="flex-grow-1">
        @yield('content')
    </main>
    
    @include('front.partials.footer')

    @stack('scripts')
    @include('layouts.footer-js')
</body>
</html>
