<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.head-page-meta', ['title' => View::hasSection('title') ? View::getSection('title') : 'Dashboard'])
    @include('layouts.head-css')
    @vite(['resources/js/app.js'])
</head>

<body @bodySetup>
    @include('layouts.layout-vertical')

    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ Main Content ] start -->
            @yield('content')
            <!-- [ Main Content ] end -->
        </div>
    </div>
    <!-- [ Main Content ] end -->

    @include('layouts.footer-block')
    
    @include('layouts.customizer')

    @include('layouts.footer-js')
    
    @stack('scripts')
</body>

</html>
