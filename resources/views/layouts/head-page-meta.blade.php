@isset($title)
<title>{{ $title }} | EliCrochet E-Commerce</title>
@endisset
<!-- [Meta] -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="description" content="@yield('meta_description', 'EliCrochet - Tienda en línea de productos de crochet hechos a mano en Ecuador.')">
<meta name="keywords" content="@yield('meta_keywords', 'EliCrochet, Crochet, Hecho a mano, Ecommerce, Ecuador, Moda, Artesanía')">
<meta name="author" content="EliCrochet">

<!-- [Open Graph] -->
<meta property="og:title" content="@yield('title', 'EliCrochet') | EliCrochet E-Commerce">
<meta property="og:description" content="@yield('meta_description', 'EliCrochet - Tienda en línea de productos de crochet hechos a mano en Ecuador.')">
<meta property="og:type" content="website">
<meta property="og:site_name" content="EliCrochet">
<meta property="og:image" content="{{ asset('/assets/images/favicon.ico') }}">

<!-- [Favicon] icon -->
<link rel="icon" href="{{asset('/assets/images/favicon.ico')}}" type="image/x-icon">
