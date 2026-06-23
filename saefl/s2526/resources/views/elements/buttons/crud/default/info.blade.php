<a
    role="button"
    title="{{ $title ?? 'Información' }}"
    class="btn btn-{{ $class_bt ?? 'info' }} {{ $disabled ?? ''}}"
    href="{{ $route ?? '#' }}"
    id="{{ $id ?? ''}}"
    target="{{ $target ?? ''}}"
    data-url="{{ $data_url ?? ''}}"
    data-id="{{ $data_id ?? ''}}"
>
    <i class="{{ $icon ?? $icon_menus['info']}}"></i>
    {{ $text ?? '' }}
</a>
