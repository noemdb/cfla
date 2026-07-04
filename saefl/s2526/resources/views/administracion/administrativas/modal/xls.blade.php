<!-- Button trigger modal -->
<button type="button" class="btn btn-success" data-toggle="modal" data-target="#xlsModal">
    <i class="{{ $icon_menus['xls'] ?? '' }} fa-1x"></i>
</button>

{!! Form::open([
    'route' => 'administracion.administrativas.list.dw.excel',
    'method' => 'POST',
    'class' => '',
    'role' => 'search',
    'target' => '_blank',
]) !!}

<!-- Modal -->
<div class="modal fade" id="xlsModal" tabindex="-1" role="dialog" aria-labelledby="xlsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="xlsModalLabel">Descarga listado de estudiantes con inscripciones
                    administrativas (XLS)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label for="list_pescolar" class="m-0 pt-2">Peŕodo Escolar a consultar</label>
                {!! Form::select('pescolar_id', $list_pescolar, old('list_pescolar'), [
                    'class' => 'form-control',
                    'id' => 'pescolar_id',
                    'placeholder' => 'Seleccione',
                    'required' => 'required',
                ]) !!}
                <label for="order" class="m-0 pt-2">Ordenado por</label>
                {!! Form::select(
                    'order',
                    ['ci_estudiant' => 'Identificador', 'lastname' => 'Apellidos y nombres'],
                    old('order'),
                    ['class' => 'form-control', 'id' => 'order_list', 'placeholder' => 'Seleccione', 'required' => 'required'],
                ) !!}
            </div>
            <div class="modal-footer">
                {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}
                <button class="btn btn-primary btn-block pt-2 mt-2" type="submit">Descargar</button>
            </div>
        </div>
    </div>
</div>

{!! Form::close() !!}
