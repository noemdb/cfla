@foreach ($groups as $group)
    @if (!empty($group['mega_menu']))
        <x-menu.mega-menu-desktop :group="$group" />
    @elseif (count($group['items'] ?? []))
        <x-menu.group-desktop :group="$group" />
    @endif
@endforeach