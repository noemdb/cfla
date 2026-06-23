{!! Form::open([
    'route' => 'administracion.asisst_controls.assit_attendances.csv.post',
    'method' => 'POST',
    'class' => 'p-1 m-1',
    'role' => 'search',
    'files' => 'true',
    'enctype' => 'multipart/form-data',
]) !!}

<div class="alert border rounded py-2 my-2">

    <div class="form-row font-weight-bold">
        <div class="col-10">
            <i class="{{ $icon_menus['csv'] ?? '' }} text-success" aria-hidden="true"></i>
            Archivo CSV
        </div>
        <div class="col-2">&nbsp;</div>
    </div>

    <div class="form-row">

        <div class="col-7">
            <div class="input-group">
                <div class="custom-file">

                    {!! Form::file('file_csv', ['class' => 'custom-file-input', 'required']) !!}
                    <label class="custom-file-label" for="inputGroupFile01">Selecciona archivo CSV</label>

                </div>
            </div>
        </div>

        <div class="col-5">
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

</div>

{!! Form::close() !!}
