<a title="{{ $title ?? '' }}" class="dropdown-item {{ $class_bt ?? '' }}" href="{{ $route ?? '#' }}" role="button">
    <i class="{{ $icon ?? '' }}"></i>
    {{ $text ?? $title }}
</a>