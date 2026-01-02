<!-- [Google Font] Family -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" id="main-font-link">
<!-- [Tabler Icons] Local -->
<link rel="stylesheet" href="{{ asset('assets/css/libs/tabler-icons.min.css') }}">
<!-- [Feather Icons] CDN Fallback -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.css">
<!-- [Font Awesome Icons] CDN Fallback -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<!-- [Material Icons] CDN Fallback -->
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

<!-- Animate.css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>


<!-- [Template CSS Files] -->
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
<link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}">
<!-- DataTables Bootstrap 5 CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<style>
    /* Custom Design Tweaks */
    
    .text-gradient-purple {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        color: transparent;
    }

    .bg-gradient-purple {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        border: none;
    }

    /* Navbar Link Size Increase */
    .navbar-nav .nav-link {
        font-size: 1.15rem; /* ~18px */
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .navbar-nav .nav-link.active,
    .navbar-nav .nav-link:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 700;
    }

    /* Global Headings Override (Optional - based on request) */
    h1, h2, h3 {
        color: #333; /* Default dark */
    }
    
    .section-title,
    h1.text-gradient,
    h2.text-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .landing-page .btn-primary {
         background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
         border: none;
    }
    
    .landing-page .btn-primary:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
         border: none;
    }
    
    .landing-page .btn-outline-primary {
        color: #764ba2;
        border-color: #764ba2;
    }
    
    .landing-page .btn-outline-primary:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: transparent;
    }
    /* Force Tabler Icons Font Family and Define it */
    @font-face {
        font-family: 'Lexend';
        src: url("{{ asset('assets/css/libs/fonts/Lexend-VariableFont_wght.ttf') }}") format('truetype');
        font-weight: 100 900;
        font-style: normal;
        font-display: swap;
    }

    @font-face {
        font-family: 'tabler-icons';
        font-style: normal;
        font-weight: 400;
        src: url('{{ asset('assets/css/libs/fonts/tabler-icons.woff2') }}') format('woff2'),
             url('{{ asset('assets/css/libs/fonts/tabler-icons.woff') }}') format('woff'),
             url('{{ asset('assets/css/libs/fonts/tabler-icons.ttf') }}') format('truetype');
    }

    .ti { 
        font-family: 'tabler-icons' !important; 
    }
</style>
