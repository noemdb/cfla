@extends('administracion.layouts.dashboard.app')

@section('title')
    SAEFL - Histórico de Pago {{ empty($representant->id) ? '' : $representant->name }}
@endsection

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary">

                @includeWhen(!empty($representant->id), 'administracion.elements.badges.bills_representant')

                <h4 class=" pb-0 mb-0">
                    <i class="{{ $icon_menus['representante'] ?? '' }} fa-1x"></i>
                    Histórico de Pago
                </h4>

                @if (!empty($representant->id))
                    <span class="small text-dark p-0 m-0">
                        <strong><span id="representant">{{ $representant->name ?? 'fallo' }}
                                ({{ $representant->ci_representant ?? 'fallo' }})</span></strong>
                    </span>
                @endif

            </div>

            <div class="card-body">

                {!! Form::open([
                    'route' => 'administracion.representants.historico',
                    'method' => 'GET',
                    'class' => 'pb-2',
                    'role' => 'search',
                ]) !!}
                <label for="representant_id" class="m-0">Representante</label>
                <div class="input-group pb-3">
                    <div class="input-group-append" style="z-index: 0;">
                        {!! Form::text('help_representante', $help_representante, [
                            'class' => 'form-control',
                            'placeholder' => 'CI o nombre',
                            'id' => 'help_representante',
                        ]) !!}
                    </div>
                    {!! Form::select('representant_id', $list_representant, $representant_id, [
                        'class' => 'form-control',
                        'id' => 'representant_id',
                        'required' => 'required',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                    <div class="input-group-append" style="z-index: 0;">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-primary" type="submit">Buscar</button>
                            <a title="Asistente para el Registro de Pagos" class="btn btn-success px-2"
                                href="{{ route('administracion.registropagos.asistent.representant.create', ['id' => $representant_id]) }}"
                                role="button">
                                <i class="{{ $icon_menus['registropagos'] }} fa-1x"></i>
                            </a>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}

                @includeWhen($representant, 'administracion.representants.table.historico')

                <div id="container_modal"></div>

            </div>
        </div>
    </main>
@endsection

@section('scripts')
    @parent

    <script type="text/javascript">
        // fill para el select representante
        $(function() {
            $('#representant_id').filterByText($('#help_representante'), true);
        });
        jQuery.fn.filterByText = function(textbox, selectSingleMatch) {
            return this.each(function() {
                var select = this;
                var options = [];
                $(select).find('option').each(function() {
                    options.push({
                        value: $(this).val(),
                        text: $(this).text()
                    });
                });
                $(select).data('options', options);
                $(textbox).bind('change keyup', function() {
                    var options = $(select).empty().scrollTop(0).data('options');
                    var search = $.trim($(this).val());
                    var regex = new RegExp(search, 'gi');

                    $.each(options, function(i) {
                        var option = options[i];
                        if (option.text.match(regex) !== null) {
                            $(select).append(
                                $('<option>').text(option.text).val(option.value)
                            );
                        }
                    });
                    if (selectSingleMatch === true &&
                        $(select).children().length === 1) {
                        $(select).children().get(0).selected = true;
                    }
                });
            });
        };
    </script>
@endsection
