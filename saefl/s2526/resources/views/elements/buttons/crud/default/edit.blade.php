<a
    role="button"
    title="{{ $title ?? 'Editar' }}"
    class="btn btn-{{ $class_bt ?? 'warning' }} {{ $disabled ?? ''}}"
    href="{{ $route ?? '#' }}"
    id="{{ $id ?? ''}}"
    target="{{ $target ?? ''}}"
    data-url="{{ $data_url ?? ''}}"
    data-id="{{ $data_id ?? ''}}"
>
    <i class="{{ $icon ?? $icon_menus['editar']}}"></i>
    {{ $text ?? '' }}
</a>
