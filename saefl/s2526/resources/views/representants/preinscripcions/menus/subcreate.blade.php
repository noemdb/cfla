@component('elements.menus.dropdown')
    @slot('title', 'Formatos Imprimibles')
    @slot('text', 'Formatos Imprimibles')
    @slot('class', 'info dropleft')
    @slot('icon', $icon_menus['crud'])
    @slot('dropdown')
        @component('elements.buttons.dropdown')
            @slot('title', 'Planilla de Inscripción')
            @slot('class_bt', 'info')
            @slot('route', '#')
            @slot('icon', $icon_menus['pdf'])
        @endcomponent
        @component('elements.buttons.dropdown')
            @slot('title', 'Ficha del Estudiante')
            @slot('class_bt', 'info')
            @slot('route', '#')
            @slot('icon', $icon_menus['pdf'])
        @endcomponent
        @component('elements.buttons.dropdown')
            @slot('title', 'Ficha del Representante')
            @slot('class_bt', 'info')
            @slot('route', '#')
            @slot('icon', $icon_menus['pdf'])
        @endcomponent
        @component('elements.buttons.dropdown')
            @slot('title', 'Contrato de Servicio')
            @slot('class_bt', 'info')
            @slot('route', '#')
            @slot('icon', $icon_menus['pdf'])
        @endcomponent
        @component('elements.buttons.dropdown')
            @slot('title', 'Normas de Convivencia')
            @slot('class_bt', 'info')
            @slot('route', '#')
            @slot('icon', $icon_menus['pdf'])
        @endcomponent
    @endslot
@endcomponent

