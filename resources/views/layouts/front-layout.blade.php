
<!DOCTYPE html>
<html lang="es">
<head>
    <title>@yield('title', 'EliCrochet')</title>
    @include('layouts.head-page-meta')
    @include('layouts.head-css')
</head>
<body class="landing-page">

    <main>
        @yield('content')
    </main>
    @include('layouts.footer-js')
</body>
</html>
