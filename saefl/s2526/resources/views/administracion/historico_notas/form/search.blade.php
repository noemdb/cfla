{!! Form::open(['route' => $route, 'method' => 'GET', 'class' => 'p-1 m-1', 'role' => 'search']) !!}
<div class="form-row font-weight-bold">
    <div class="col-2">Identificador</div>
    <div class="col-4">Estudiante</div>
    <div class="col-2">Plan de Estudio</div>
    <div class="col-2">Fecha Inicial</div>
    <div class="col-2">&nbsp;</div>
</div>
<div class="form-row">
    <div class="col-2">
        {!! Form::text('help_estudiant', $help_estudiant, [
            'class' => 'form-control small',
            'placeholder' => 'CI o nombre',
            'id' => 'help_estudiant',
        ]) !!}
    </div>
    <div class="col-4">
        {!! Form::select('estudiant_id', $list_estudiant, $estudiant_id, [
            'class' => 'form-control',
            'id' => 'estudiant_id',
            'required' => 'required',
            'placeholder' => 'Seleccione',
        ]) !!}
    </div>
    <div class="col-2">
        {!! Form::select('pestudio_id', $list_pestudio, $pestudio_id, [
            'id' => 'pestudio_id',
            'class' => 'form-control',
            'placeholder' => 'Seleccione',
            'required',
        ]) !!}
    </div>
    <div class="col-2">
        {!! Form::text('finicial', $finicial, [
            'class' => 'form-control datepicker',
            'required',
            'readonly',
            'maxlength' => '10',
        ]) !!}
    </div>
    <div class="col-2">
        <button class="btn btn-primary my-2 my-sm-0 btn-block" type="submit">Buscar</button>
    </div>
</div>
{!! Form::close() !!}

@section('scripts')
    @parent
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
