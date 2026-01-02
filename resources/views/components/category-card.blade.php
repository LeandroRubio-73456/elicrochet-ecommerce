@props(['category'])

<a href="{{ route('category.show', $category->slug) }}" class="category-card-modern">
    <div class="category-icon">
        <i class="ti ti-{{ isset($category->icon) ? preg_replace('/^(ti-?|ti\s)/', '', $category->icon) : 'layout-grid' }}"></i>
    </div>
    <h3 class="category-name">{{ $category->name }}</h3>
    <p class="category-desc">{{ Str::limit($category->description, 50) }}</p>
    <div class="category-arrow">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M5 12h14M12 5l7 7-7 7"/>
        </svg>
    </div>
</a>
