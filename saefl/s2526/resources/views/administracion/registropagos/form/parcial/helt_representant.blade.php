<div class=" alert-secondary p-2 rounded rounded-bottom-0">
    <label for="representant_id" class="m-0 font-weight-bold">Representante</label>
    <div class="input-group pb-1">
        <div class="input-group-append" style="z-index: 0;">
            {!! Form::text('help_representante', $help_representante, [
                'class' => 'form-control',
                'placeholder' => 'CI o nombre',
                'id' => 'help_representante',
            ]) !!}
        </div>
        {!! Form::select('id', $list_representant, $representant_id, [
            'class' => 'form-control',
            'id' => 'representant_id',
            'required' => 'required',
            'placeholder' => 'Seleccione',
        ]) !!}
        <div class="input-group-append" style="z-index: 0;">
            <a title="Buscar" class="btn btn-primary" id="btn_search" href="#" role="button">
                <i class="{{ $icon_menus['buscar'] }} fa-1x"></i> Buscar
            </a>
            <a title="Hitórico de Pagos" class="btn btn-info" id="btn_modal_registropagos"
                href="{{ route('administracion.representants.historico', ['representant_id' => $representant_id]) }}"
                role="button">
                <i class="{{ $icon_menus['registropagos'] }} fa-1x"></i>
            </a>
        </div>
    </div>
</div>

@section('scripts')
    @parent

    <script type="text/javascript">
        $('#btn_search').click(function(e) {
            e.preventDefault();
            var representant_id = $('#representant_id').val();
            console.log(representant_id);
            var url = "{{ route('administracion.registropagos.asistent.representant.create', ['id' => '_rid_']) }}";
            url = url.replace('_rid_', representant_id);
            window.open(url, '_self');
        });

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
