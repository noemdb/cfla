@extends('administracion.layouts.dashboard.app')

@section('main')
    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header alert-secondary">

                @includeWhen(!empty($estudiant->id), 'administracion.elements.badges.bills_estudiant')

                <h4 class=" pb-0 mb-0">
                    <i class="{{ $icon_menus['estudiante'] ?? '' }} fa-1x"></i>
                    Histórico de Pago
                </h4>
                <span class="small text-dark p-0 m-0">
                    <strong><span id="user_estudiant">{{ $estudiant->fullname ?? 'fallo' }}
                            ({{ $estudiant->ci_estudiant ?? 'fallo' }})</span></strong>
                    <br>
                    @if (!empty($estudiant))
                        <span class=" pl-2">
                            <i class="{{ $icon_menus['representante'] ?? '' }} fa-1x"></i>
                            {{ $estudiant->representant->name ?? 'fallo' }}
                        </span>
                        <a title="Histórico de pagos" class="btn-link"
                            href="{{ route('administracion.representants.historico', ['representant_id' => $estudiant->representant->id]) }}"
                            role="button">
                            <i
                                class="{{ $icon_menus['historico'] ?? '' }} fa-1x btn btn-sm btn-outline-light text-primary"></i>
                        </a>
                    @endif
                </span>
            </div>

            <div class="card-body">

                {!! Form::open([
                    'route' => 'administracion.estudiants.historico',
                    'method' => 'GET',
                    'class' => 'pb-2',
                    'role' => 'search',
                ]) !!}
                <label for="estudiant_id" class="m-0">Estudiante</label>
                <div class="input-group pb-3">
                    <div class="input-group-append" style="z-index: 0;">
                        {!! Form::text('help_estudiant', $help_estudiant, [
                            'class' => 'form-control small',
                            'placeholder' => 'CI o nombre',
                            'id' => 'help_estudiant',
                        ]) !!}
                    </div>
                    {!! Form::select('estudiant_id', $list_estudiant, $estudiant_id, [
                        'class' => 'form-control',
                        'id' => 'estudiant_id',
                        'required' => 'required',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                    <div class="input-group-append" style="z-index: 0;">
                        <button class="btn btn-primary my-2 my-sm-0" type="submit">Buscar</button>
                    </div>
                </div>
                {!! Form::close() !!}

                @include('administracion.estudiants.table.historico')

            </div>
        </div>
    </main>
@endsection


@section('scripts')
    @parent
    <script type="text/javascript">
        document.title = 'SAEFL - Histórico de Pago {{ empty($estudiant->id) ? '' : $estudiant->fullname }}';
    </script>

    <script type="text/javascript">
        $(function() {
            $('#estudiant_id').filterByText($('#help_estudiant'), true);
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
