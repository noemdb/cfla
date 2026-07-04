<div class="card-header p-0 m-0 mb-3">
    {!! Form::open([
        'route' => $route,
        'method' => 'POST',
        'class' => 'p-1 m-1',
        'role' => 'search',
        'files' => 'true',
        'enctype' => 'multipart/form-data',
    ]) !!}
    <div class="form-row font-weight-bold">
        <div class="col-10">
            <i class="{{ $icon_menus['xls'] ?? '' }} text-success" aria-hidden="true"></i>
            Archivo XLS
        </div>
        <div class="col-2">&nbsp;</div>
    </div>
    <div class="form-row">

        <div class="col-10">
            <div class="input-group">
                <div class="custom-file">
                    {!! Form::file('file_csv', ['class' => 'custom-file-input', 'required']) !!}
                    <label class="custom-file-label" for="inputGroupFile01">Selecciona archivo XLS</label>
                </div>
            </div>
        </div>

        <div class="col-2">
            <div class="btn-group btn-group btn-block">
                <button class="btn btn-info my-2 my-sm-0 btn-block" type="submit">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    Buscar
                </button>
                <a id="" class="btn btn-light" href="{{ url()->current() }}" role="button"
                    title="Refrescar la página">
                    <i class="fas fa-redo" aria-hidden="true"></i>
                </a>
                {{-- <button type="button" class="btn btn-light" data-toggle="modal" data-target="#modal_search_help">
                        <i class="{{ $icon_menus['ayuda'] }}"></i>
                    </button> --}}
            </div>
            {{-- @include('administracion.preinscripcions.partials.search_help') --}}
        </div>

    </div>

    {!! Form::close() !!}
</div>
