@component('elements.widgets.modal')
    @slot('classH','danger')
    @slot('id',$modal_id)
    @slot('title','Errores encontrados')
    @slot('title_icon',$icon_menus['danger'])
    @slot('close',true)
    @slot('size','modal-md')
    @slot('body')
        @foreach ($errors as $error)
            @include('administracion.preinscripcions.table.carga_csv.errors.error')
        @endforeach
    @endslot
@endcomponent
