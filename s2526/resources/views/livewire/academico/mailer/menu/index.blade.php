<button class="btn btn-primary btn-sm" wire:click="create()">
    <i class="{{ $icon_menus['nuevo'] }} fa-1x"></i>
</button>
{{-- <button class="btn btn-info btn-sm" title="">
    <i class="{{ $icon_menus['info'] }} fa-1x"></i>
</button> --}}


@component('elements.buttons.default')
    @slot('title', 'Ir atrás')
    @slot('class_bt', 'dark')
    @slot('route', url()->previous())
    @slot('icon', 'fas fa-chevron-left')
@endcomponent


<button type="button" class="btn btn-info btn-sm float-right" data-toggle="modal" data-target="#paymentAnyErrors">
  <i class="{{ $icon_menus['ayuda'] ?? ''}} fa-1x"></i>
</button>



{{-- @component('elements.buttons.default')
    @slot('title', 'Refrescar la página')
    @slot('class_bt', 'dark')
    @slot('route', url()->current())
    @slot('icon', 'fas fa-redo')
@endcomponent --}}
