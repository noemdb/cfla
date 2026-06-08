@component('elements.widgets.modal')
@slot('classH','secondary')
@slot('id','modal_search_help')
@slot('title','Ejemplo de la estructura del archivo')
@slot('close',true)
@slot('size','modal-xl')
    @slot('body')
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active font-weight-bold" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Archivo CSV</a>
            <a class="nav-item nav-link font-weight-bold" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Archivo XLS</a>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                @include('administracion.mbancarios.partials.tabcsv')

            </div>
            <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                @include('administracion.mbancarios.partials.tabxls')
            </div>
        </div>
    @endslot
@endcomponent
