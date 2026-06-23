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
        <div class="col-6">Archivo CSV</div>
        <div class="col-2">Delimitador</div>
        <div class="col-2">Codificación</div>
        <div class="col-2">&nbsp;</div>
    </div>
    <div class="form-row">

        <div class="col-6">
            <div class="input-group">
                <div class="custom-file">
                    {!! Form::file('file_csv', ['class' => 'custom-file-input', 'required']) !!}
                    <label class="custom-file-label" for="inputGroupFile01">Selecciona CSV</label>
                </div>
            </div>
        </div>

        <div class="col-2">
            {!! Form::select('delimiter', [';' => ';', ',' => ',', '.' => '.'], $delimiter, [
                'required',
                'class' => 'form-control',
                'id' => 'delimiter',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>

        <div class="col-2">
            {!! Form::select('input_encoding', ['UTF-8' => 'UTF-8', 'ISO-8859-1' => 'ISO-8859-1'], $input_encoding, [
                'required',
                'class' => 'form-control',
                'id' => 'input_encoding',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>


        <div class="col-2">
            <div class="btn-group btn-group btn-block">
                <button class="btn btn-info my-2 my-sm-0 btn-block" type="submit">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    Buscar
                </button>
                <button type="button" class="btn btn-light" data-toggle="modal" data-target="#modal_search_help">
                    <i class="{{ $icon_menus['ayuda'] }}"></i>
                </button>
            </div>
            @include('administracion.mbancarios.partials.search_help')
        </div>

    </div>

    {!! Form::close() !!}
</div>
