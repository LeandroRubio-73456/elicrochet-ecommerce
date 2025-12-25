@props(['title', 'label', 'link' => null, 'linkText' => 'Ver todo'])

@if($link)
<div class="section-header-flex mb-5">
    <div>
        <span class="section-label">{{ $label }}</span>
        <h2 class="section-title">{{ $title }}</h2>
    </div>
    <a href="{{ $link }}" class="btn-modern btn-ghost">
        {{ $linkText }}
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M5 12h14M12 5l7 7-7 7"/>
        </svg>
    </a>
</div>
@else
<div class="section-header text-center mb-5">
    <span class="section-label">{{ $label }}</span>
    <h2 class="section-title">{{ $title }}</h2>
</div>
@endif
