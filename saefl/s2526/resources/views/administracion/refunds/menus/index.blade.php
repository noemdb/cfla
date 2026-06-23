@component('elements.buttons.default')
    @slot('title', 'Nuevo')
    @slot('class_bt', 'primary')
    @slot('route', $route)
    @slot('icon', $icon_menus['nuevo'])
@endcomponent

@component('elements.buttons.default')
    @slot('title', 'Refrescar la página')
    @slot('class_bt', 'dark')
    @slot('route', url()->current())
    @slot('icon', 'fas fa-redo')
@endcomponent

@component('elements.buttons.default')
    @slot('title', 'Ir atrás')
    @slot('class_bt', 'dark')
    @slot('route', url()->previous())
    @slot('icon', 'fas fa-chevron-left')
@endcomponent


{{-- <button type="button" class="btn btn-info btn-sm float-right" data-toggle="modal" data-target="#paymentAnyErrors">
  <i class="{{ $icon_menus['ayuda'] ?? ''}} fa-1x"></i>
</button> --}}



{{-- @component('elements.buttons.default')
    @slot('title', 'Refrescar la página')
    @slot('class_bt', 'dark')
    @slot('route', url()->current())
    @slot('icon', 'fas fa-redo')
@endcomponent --}}
