<!-- [Google Font] Family -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" id="main-font-link">
<!-- [Tabler Icons] https://tablericons.com -->
<link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
<!-- [Feather Icons] https://feathericons.com -->
<link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
<!-- [Font Awesome Icons] https://fontawesome.com/icons -->
<link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
<!-- [Material Icons] https://fonts.google.com/icons -->
<link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">

<link rel="stylesheet" href="{{ asset('assets/fonts/themify-icons.css') }}">

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
</style>
