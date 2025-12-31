<style>
/* CRITICAL variables */
:root {
    --color-primary: #C16244;
    --color-text: #1E1E1E;
    --color-bg-alt: #FDFBF9;
    --bs-gutter-x: 1.5rem;
}

/* CRITICAL reset/layout */
*, ::after, ::before { box-sizing: border-box; }
body { margin: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }

/* CRITICAL Grid System */
.container { width: 100%; padding-right: var(--bs-gutter-x, .75rem); padding-left: var(--bs-gutter-x, .75rem); margin-right: auto; margin-left: auto; }
@media (min-width: 576px) { .container { max-width: 540px; } }
@media (min-width: 768px) { .container { max-width: 720px; } }
@media (min-width: 992px) { .container { max-width: 960px; } }
@media (min-width: 1200px) { .container { max-width: 1140px; } }

.row { --bs-gutter-x: 1.5rem; --bs-gutter-y: 0; display: flex; flex-wrap: wrap; margin-top: calc(var(--bs-gutter-y) * -1); margin-right: calc(var(--bs-gutter-x) / -2); margin-left: calc(var(--bs-gutter-x) / -2); }
.col-lg-4, .col-lg-6, .col-md-6 { position: relative; width: 100%; padding-right: calc(var(--bs-gutter-x) / 2); padding-left: calc(var(--bs-gutter-x) / 2); }
@media (min-width: 768px) { .col-md-6 { flex: 0 0 auto; width: 50%; } }
@media (min-width: 992px) {
    .col-lg-4 { flex: 0 0 auto; width: 33.33333333%; }
    .col-lg-6 { flex: 0 0 auto; width: 50%; }
}

/* CRITICAL Navbar */
.navbar { position: relative; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; padding-top: .5rem; padding-bottom: .5rem; background-color: #fff; }
.navbar > .container { display: flex; flex-wrap: inherit; align-items: center; justify-content: space-between; }
.navbar-brand { padding-top: .3125rem; padding-bottom: .3125rem; margin-right: 1rem; font-size: 1.25rem; white-space: nowrap; }
.d-inline-block { display: inline-block !important; }
.align-middle { vertical-align: middle !important; }

/* Sticky Top prevention of jump */
.sticky-top { position: sticky; top: 0; z-index: 1020; }

/* CRITICAL Hero Image - Prevents 0.12 CLS */
.hero-image { width: 100%; height: auto; aspect-ratio: 780/584; object-fit: cover; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
.hero-image-wrapper { position: relative; min-height: 290px; } /* Safety min-height */
.min-vh-90 { min-height: 90vh; } /* Critical Layout Height */

/* CRITICAL Bootstrap Utilities (Prevent Layout Shift while Bootstrap loads) */
.d-flex { display: flex !important; }
.flex-column { flex-direction: column !important; }
.flex-grow-1 { flex-grow: 1 !important; }
.min-vh-100 { min-height: 100vh !important; }
.align-items-center { align-items: center !important; }
.justify-content-center { justify-content: center !important; }
.justify-content-between { justify-content: space-between !important; }
.py-5 { padding-top: 3rem !important; padding-bottom: 3rem !important; }
.mb-5 { margin-bottom: 3rem !important; }
.mb-4 { margin-bottom: 1.5rem !important; }
.gap-2 { gap: 0.5rem !important; }

@media (min-width: 992px) {
    .mb-lg-0 { margin-bottom: 0 !important; }
}

/* CRITICAL Typography & Hero Content (Prevents Text Expansion CLS) */
.hero-content { max-width: 600px; }
.hero-title { font-size: clamp(2.5rem, 5vw, 4rem); font-weight: 700; line-height: 1.1; margin-bottom: 1.5rem; }
.hero-subtitle { font-size: 1.25rem; color: #766352; max-width: 500px; margin-bottom: 3rem; }
.badge-custom { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; background: white; border-radius: 100px; font-size: 14px; font-weight: 500; color: var(--color-primary); box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
.hero-actions { display: flex; gap: 1rem; flex-wrap: wrap; }
.btn-modern { display: inline-flex; align-items: center; gap: 8px; padding: 14px 28px; font-size: 16px; font-weight: 500; border-radius: 12px; border: none; cursor: pointer; text-decoration: none; }
.btn-primary { background: var(--color-primary); color: white; }
.btn-ghost { background: transparent; color: var(--color-text); border: 2px solid var(--color-text); }
.text-gradient { background: linear-gradient(135deg, var(--color-primary) 0%, #766352 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
</style>
