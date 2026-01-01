<style>
/* CRITICAL: Root Variables from main-modern.css */
:root {
    --color-primary: #C16244;
    --color-primary-dark: #a04e35;
    --color-primary-ligthtwo: #ff9e81;
    --color-primary-light: #F9DEC2;
    --color-text: #1E1E1E;
    --color-text-light: #766352;
    --color-border: #e5e7eb;
    --color-bg-soft: #F7EFE9;
    --color-bg-alt: #FDFBF9;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --bs-gutter-x: 1.5rem;
    --bs-gutter-y: 0;
    --bs-body-font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    --bs-body-font-size: 1rem;
    --bs-body-font-weight: 400;
    --bs-body-line-height: 1.5;
    --bs-body-color: #212529;
    --bs-body-bg: #fff;
}

/* CRITICAL: Tabler Icons Font Face */
@font-face {
    font-display: swap;
    font-family: "tabler-icons";
    font-style: normal;
    font-weight: 400;
    src: url("{{ asset('assets/css/libs/fonts/tabler-icons.woff2') }}") format("woff2"),
         url("{{ asset('assets/css/libs/fonts/tabler-icons.woff') }}") format("woff"),
         url("{{ asset('assets/css/libs/fonts/tabler-icons.ttf') }}") format("truetype");
}
.ti {
    font-family: "tabler-icons" !important;
    speak: none;
    font-style: normal;
    font-weight: normal;
    font-variant: normal;
    text-transform: none;
    line-height: 1;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
/* Key icons used in Navbar */
.ti-shopping-cart:before { content: "\ea01"; } /* Fail-safe default if mapping differs, but font loads glyphs */
.ti-user:before { content: "\eb56"; }
.ti-menu-2:before { content: "\ebc6"; }
.ti-search:before { content: "\eb0f"; }

/* CRITICAL: Reset & Base */
*, ::after, ::before { box-sizing: border-box; }
body { margin: 0; font-family: var(--bs-body-font-family); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); line-height: var(--bs-body-line-height); color: var(--color-text); background-color: var(--bs-body-bg); -webkit-text-size-adjust: 100%; -webkit-tap-highlight-color: transparent; }
a { color: var(--color-primary); text-decoration: none; }
a:hover { color: var(--color-primary-dark); }
img, svg { vertical-align: middle; }

/* CRITICAL: Layout & Grid */
.container, .container-fluid, .container-lg, .container-md, .container-sm, .container-xl, .container-xxl { width: 100%; padding-right: calc(var(--bs-gutter-x)* .5); padding-left: calc(var(--bs-gutter-x)* .5); margin-right: auto; margin-left: auto; }
@media (min-width: 576px) { .container { max-width: 540px; } }
@media (min-width: 768px) { .container { max-width: 720px; } }
@media (min-width: 992px) { .container { max-width: 960px; } }
@media (min-width: 1200px) { .container { max-width: 1140px; } }
.row { --bs-gutter-x: 1.5rem; --bs-gutter-y: 0; display: flex; flex-wrap: wrap; margin-top: calc(var(--bs-gutter-y)* -1); margin-right: calc(var(--bs-gutter-x)* -.5); margin-left: calc(var(--bs-gutter-x)* -.5); }
.row > * { box-sizing: border-box; flex-shrink: 0; width: 100%; max-width: 100%; padding-right: calc(var(--bs-gutter-x)* .5); padding-left: calc(var(--bs-gutter-x)* .5); margin-top: var(--bs-gutter-y); }
.col-12 { flex: 0 0 auto; width: 100%; }
.col-md-6 { flex: 0 0 auto; width: 50%; }
@media (min-width: 768px) { .col-md-6 { flex: 0 0 auto; width: 50%; } }
@media (min-width: 992px) { .col-lg-4 { flex: 0 0 auto; width: 33.33333333%; } .col-lg-6 { flex: 0 0 auto; width: 50%; } .col-lg-3 { flex: 0 0 auto; width: 25%; } }

/* CRITICAL: Navbar */
.navbar { position: relative; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; padding-top: .5rem; padding-bottom: .5rem; background-color: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
.navbar > .container { display: flex; flex-wrap: inherit; align-items: center; justify-content: space-between; }
.navbar-brand { padding-top: .3125rem; padding-bottom: .3125rem; margin-right: 1rem; font-size: 1.25rem; white-space: nowrap; }
.navbar-toggler { padding: .25rem .75rem; font-size: 1.25rem; line-height: 1; background-color: transparent; border: 1px solid transparent; border-radius: .25rem; transition: box-shadow .15s ease-in-out; }
.navbar-toggler:focus { text-decoration: none; outline: 0; box-shadow: 0 0 0 .25rem; }
.collapse:not(.show) { display: none; }
.navbar-collapse { flex-basis: 100%; flex-grow: 1; align-items: center; }
.navbar-nav { display: flex; flex-direction: column; padding-left: 0; margin-bottom: 0; list-style: none; }
.nav-link { display: block; padding: .5rem 1rem; color: var(--color-text); text-decoration: none; transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out; font-weight: 500; }
.nav-link:hover, .nav-link:focus { color: var(--color-primary); }
.navbar-nav .dropdown-menu { position: absolute; }
.dropdown-menu { position: absolute; z-index: 1000; display: none; min-width: 10rem; padding: .5rem 0; margin: 0; font-size: 1rem; color: #212529; text-align: left; list-style: none; background-color: #fff; background-clip: padding-box; border: 1px solid rgba(0,0,0,.15); border-radius: .25rem; }
@media (min-width: 992px) { 
    .navbar-expand-lg { flex-wrap: nowrap; justify-content: flex-start; }
    .navbar-expand-lg .navbar-nav { flex-direction: row; }
    .navbar-expand-lg .navbar-collapse { display: flex !important; flex-basis: auto; }
    .navbar-expand-lg .navbar-toggler { display: none; }
    .navbar-expand-lg .offcanvas { position: inherit; bottom: 0; z-index: 1000; flex-grow: 1; visibility: visible !important; background-color: transparent; border-right: 0; border-left: 0; transition: none; transform: none; }
    .navbar-expand-lg .offcanvas-top, .navbar-expand-lg .offcanvas-bottom { height: auto; border-top: 0; border-bottom: 0; }
    .navbar-expand-lg .offcanvas-body { display: flex; flex-grow: 0; padding: 0; overflow-y: visible; }
}

/* CRITICAL: Utilities & Helpers */
.d-flex { display: flex !important; }
.d-none { display: none !important; }
.d-block { display: block !important; }
.d-inline-block { display: inline-block !important; }
.align-items-center { align-items: center !important; }
.justify-content-center { justify-content: center !important; }
.justify-content-between { justify-content: space-between !important; }
.flex-column { flex-direction: column !important; }
.flex-grow-1 { flex-grow: 1 !important; }
.position-relative { position: relative !important; }
.position-absolute { position: absolute !important; }
.top-0 { top: 0 !important; }
.start-100 { left: 100% !important; }
.translate-middle { transform: translate(-50%, -50%) !important; }
.w-100 { width: 100% !important; }
.h-100 { height: 100% !important; }
.mw-100 { max-width: 100% !important; }
.m-0 { margin: 0 !important; }
.mt-2 { margin-top: .5rem !important; }
.mb-0 { margin-bottom: 0 !important; }
.mb-2 { margin-bottom: .5rem !important; }
.mb-3 { margin-bottom: 1rem !important; }
.my-3 { margin-top: 1rem !important; margin-bottom: 1rem !important; }
.mx-auto { margin-right: auto !important; margin-left: auto !important; }
.p-0 { padding: 0 !important; }
.px-2 { padding-right: .5rem !important; padding-left: .5rem !important; }
.px-3 { padding-right: 1rem !important; padding-left: 1rem !important; }
.py-2 { padding-top: .5rem !important; padding-bottom: .5rem !important; }
.pb-5 { padding-bottom: 3rem !important; }
.gap-2 { gap: .5rem !important; }
.text-center { text-align: center !important; }
.text-end { text-align: right !important; }
.fw-bold { font-weight: 700 !important; }
.fw-semibold { font-weight: 600 !important; }
.fs-4 { font-size: 1.5rem !important; }
.fs-5 { font-size: 1.25rem !important; }
.text-muted { color: #6c757d !important; }
.text-danger { color: #dc3545 !important; }
.bg-danger { background-color: #dc3545 !important; }
.rounded-circle { border-radius: 50% !important; }
.rounded-pill { border-radius: 50rem !important; }
.shadow-sm { box-shadow: var(--shadow-sm) !important; }
.shadow { box-shadow: var(--shadow-md) !important; }
.border-0 { border: 0 !important; }
.img-fluid { max-width: 100%; height: auto; }

/* CRITICAL: Buttons */
.btn { display: inline-block; font-weight: 400; line-height: 1.5; color: #212529; text-align: center; text-decoration: none; vertical-align: middle; cursor: pointer; user-select: none; background-color: transparent; border: 1px solid transparent; padding: .375rem .75rem; font-size: 1rem; border-radius: .375rem; transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out; }
.btn-primary { color: #fff; background-color: var(--color-primary); border-color: var(--color-primary); }
.btn-primary:hover { color: #fff; background-color: var(--color-primary-dark); border-color: var(--color-primary-dark); }
.btn-outline-primary { color: var(--color-primary); border-color: var(--color-primary); }
.btn-outline-primary:hover { color: #fff; background-color: var(--color-primary); border-color: var(--color-primary); }
.btn-sm { padding: .25rem .5rem; font-size: .875rem; border-radius: .2rem; }

/* Sticky Top */
.sticky-top { position: sticky; top: 0; z-index: 1020; }

</style>
