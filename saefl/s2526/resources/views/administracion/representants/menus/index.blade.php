@component('elements.buttons.default')
    @slot('title', 'Ir atrás')
    @slot('class_bt', 'dark')
    @slot('route', url()->previous())
    @slot('icon', 'fas fa-chevron-left')
@endcomponent

@component('elements.buttons.default')
    @slot('title', 'Refrescar la página')
    @slot('class_bt', 'dark')
    @slot('route', url()->current())
    @slot('icon', 'fas fa-redo')
@endcomponent


<!-- Agregar este botón al menú existente -->
<a title="Timeline de Pagos" class="btn btn-info btn-sm" href="{{ route('administracion.representants.timeline') }}" role="button">
    <i class="fas fa-timeline"></i> Timeline
</a>
