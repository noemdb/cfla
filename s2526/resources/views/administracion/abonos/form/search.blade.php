<div class="card-header p-0 m-0 mb-3">
    {!! Form::open(['route' => $route, 'method' => 'GET', 'class' => 'p-1 m-1', 'role' => 'search']) !!}
    <div class="form-row font-weight-bold">
        {{-- <div class="col-2">Fecha Inicial</div>
            <div class="col-2">Fecha Final</div> --}}
        <div class="col-2">Banco</div>
        <div class="col-2">Referencia</div>
        <div class="col-2">Identificador</div>
        <div class="col-2">Estado</div>
        <div class="col-2">A. Matrícula</div>
        <div class="col-1">&nbsp;</div>
    </div>
    <div class="form-row">
        <div class="col-2">
            {!! Form::select('banco_id', $list_banco, $banco_id, [
                'class' => 'form-control',
                'id' => 'banco_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        <div class="col-2">
            {!! Form::text('number_i_pay', $number_i_pay, [
                'class' => 'form-control',
                'placeholder' => 'Referencia',
                'id' => 'number_i_pay',
            ]) !!}
        </div>
        <div class="col-2">
            {!! Form::text('ci_representant', $ci_representant, [
                'class' => 'form-control',
                'placeholder' => 'CI Representant',
                'id' => 'ci_representant',
                'maxlength' => '10',
            ]) !!}
        </div>
        <div class="col-2">
            {!! Form::select('state', ['APLICADO' => 'APLICADO', 'NO APLICADO' => 'NO APLICADO'], $state, [
                'class' => 'form-control',
                'id' => 'state',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        <div class="col-2">
            {!! Form::select('status_matriculations', ['1' => 'SI', '0' => 'NO'], $status_matriculations ?? null, [
                'class' => 'form-control',
                'id' => 'status_matriculations',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>

        <div class="col-1">
            <div class="btn-group">
                <button class="btn btn-primary my-2 my-sm-0 btn-block" type="submit">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </button>
                <a id="btn_tools" href="#" class="btn btn-info" title="Más opciones..." data-toggle="modal"
                    data-target="#filters">
                    <i class="fas fa-filter" aria-hidden="true"></i>
                </a>
                <a id="" class="btn btn-light" href="{{ url()->current() }}" role="button"
                    title="Refrescar la página">
                    <i class="fas fa-redo" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>

    @include('administracion.abonos.form.modal.filters')

    {!! Form::close() !!}
</div>

@section('scripts')
    @parent

    <script type="text/javascript">
        //btn para ir a editar los datos del EST
        $(document).ready(function() {
            $('.btn-xls').click(function(e) {
                e.preventDefault();
                var banco_id = $('#banco_id').val(); //console.log(ci_estudiant);
                var number_i_pay = $('#number_i_pay').val(); //console.log(ci_estudiant);
                var ci_representant = $('#ci_representant').val(); //console.log(ci_estudiant);
                var state = $('#state').val(); //console.log(ci_estudiant);
                var status_matriculations = $('#status_matriculations').val(); //console.log(ci_estudiant);
                var finicial = $('#finicial').val(); //console.log(ci_estudiant);
                var ffinal = $('#ffinal').val(); //console.log(ci_estudiant);
                var dataString = '?banco_id=' + banco_id + '&finicial=' + finicial + '&ffinal=' + ffinal +
                    '&number_i_pay=' + number_i_pay + '&ci_representant=' + ci_representant + '&state=' +
                    state + '&status_matriculations=' + status_matriculations; //console.log(dataString);
                var url = "{{ route('administracion.abonos.list.abono.dw.excel') }}" +
                dataString; //console.log(url);
                window.open(url, '_sefl');
            });
        });
    </script>
@endsection
