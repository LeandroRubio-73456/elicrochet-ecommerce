<!-- [LCP Optimization] Preload Logo & Hero -->
<link rel="preload" href="{{ asset('Logo.webp') }}" as="image" fetchpriority="high">
<link rel="preload" as="image" href="{{ asset('assets/images/banner-mobile.avif') }}" media="(max-width: 450px)" fetchpriority="high">
<link rel="preload" as="image" href="{{ asset('assets/images/banner.avif') }}" media="(min-width: 451px)" fetchpriority="high">

<!-- [Google Font] Family - REMOVED FOR PERFORMANCE -->



<!-- [Preload] Critical Assets -->
<link rel="preload" href="{{ asset('assets/css/libs/bootstrap.min.css') }}" as="style">
<link rel="preload" href="{{ asset('css/main-modern.css') }}" as="style">
<link rel="preload" href="{{ asset('assets/css/libs/fonts/tabler-icons.woff2') }}" as="font" type="font/woff2" crossorigin>

<!-- [Tabler Icons] Local with font-display: swap -->
<link rel="stylesheet" href="{{ asset('assets/css/libs/tabler-icons.min.css') }}">

<!-- [Bootstrap] 5.3.2 Local -->
<link rel="stylesheet" href="{{ asset('assets/css/libs/bootstrap.min.css') }}">

<!-- [Template CSS] Main Modern -->
<link rel="stylesheet" href="{{ asset('css/main-modern.css') }}">



<style>
    /* Custom Design Tweaks - EliCrochet Palette */
    :root {
        --eli-bg-light: #F9F1E9;
        --eli-accent-light: #F9DEC2;
        --eli-muted: #BAA794;
        --eli-black: #000000;
        --eli-dark: #1E1E1E;
        --eli-primary: #C16244;
        --eli-white: #FFFFFF;
    }

    body.landing-page {
        background-color: var(--eli-bg-light);
        color: var(--eli-dark);
    }

    /* Override Text Colors */
    h1, h2, h3, h4, h5, h6 {
        color: var(--eli-black);
    }

    .text-muted {
        color: var(--eli-muted) !important;
    }
    
    .bg-light {
        background-color: var(--eli-bg-light) !important;
    }

    .bg-white {
        background-color: var(--eli-white) !important;
    }

    /* Primary Color Overrides */
    .text-primary {
        color: var(--eli-primary) !important;
    }
    
    .bg-primary {
        background-color: var(--eli-primary) !important;
    }

    .bg-primary.bg-opacity-10 {
        background-color: rgba(193, 98, 68, 0.1) !important; /* #C16244 with opacity */
    }

    /* Gradient Re-mapping */
    .text-gradient-purple {
        background: linear-gradient(135deg, #C16244 0%, #BAA794 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        color: transparent;
    }

    .bg-gradient-purple {
        background: linear-gradient(135deg, #C16244 0%, #BAA794 100%) !important;
        border: none;
    }

    /* Navbar Link Size Increase & Colors */
    .navbar-nav .nav-link {
        font-size: 1.15rem; /* ~18px */
        font-weight: 500;
        transition: all 0.3s ease;
        color: var(--eli-dark);
    }

    .navbar-nav .nav-link.active,
    .navbar-nav .nav-link:hover {
        background: linear-gradient(135deg, #C16244 0%, #BAA794 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 700;
    }

    .section-title,
    h1.text-gradient,
    h2.text-gradient {
        background: linear-gradient(135deg, #C16244 0%, #BAA794 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    /* Buttons */
    .landing-page .btn-primary {
         background: var(--eli-primary);
         border: none;
         color: var(--eli-white);
    }
    
    .landing-page .btn-primary:hover {
        background: #a04e35; /* Darker shade of #C16244 */
         border: none;
    }
    
    .landing-page .btn-outline-primary {
        color: var(--eli-primary);
        border-color: var(--eli-primary);
    }
    
    .landing-page .btn-outline-primary:hover {
        background: var(--eli-primary);
        color: var(--eli-white);
        border-color: transparent;
    }

    /* Additional Primary Variants */
    .bg-light-primary {
        background-color: rgba(193, 98, 68, 0.1) !important;
        color: var(--eli-primary) !important;
    }

    .border-primary {
        border-color: var(--eli-primary) !important;
    }
    
    .text-primary, .text-primary-hover:hover {
        color: var(--eli-primary) !important;
    }
    
    /* Inputs focus state */
    .form-control:focus, .form-select:focus {
        border-color: var(--eli-primary);
        box-shadow: 0 0 0 0.25rem rgba(193, 98, 68, 0.25);
    }
    
    .form-check-input:checked {
        background-color: var(--eli-primary);
        border-color: var(--eli-primary);
    }

    /* Cards */
    .card {
        border-color: var(--eli-accent-light);
    }
</style>
