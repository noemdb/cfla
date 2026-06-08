@component('elements.widgets.modal')
    @slot('classH','danger')
    @slot('id',$modal_id)
    @slot('title','Errores encontrados')
    @slot('title_icon',$icon_menus['danger'])
    @slot('close',true)
    @slot('size','modal-md')
    @slot('body')
        @foreach ($row_error as $data)
            @include('administracion.mbancarios.table.carga_xls.errors.error')
        @endforeach
    @endslot
@endcomponent
