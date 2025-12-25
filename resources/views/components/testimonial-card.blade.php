@props(['name', 'role' => 'Cliente verificado', 'text', 'image' => null, 'rating' => 5])

<div class="testimonial-modern">
    <div class="testimonial-rating">
        @for($i = 0; $i < $rating; $i++)
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>
        @endfor
    </div>
    <p class="testimonial-text">
        "{{ $text }}"
    </p>
    <div class="testimonial-author">
        <img src="{{ $image ?? 'https://ui-avatars.com/api/?name='.urlencode($name).'&background=random' }}" alt="{{ $name }}">
        <div>
            <div class="author-name">{{ $name }}</div>
            <div class="author-role">{{ $role }}</div>
        </div>
    </div>
</div>
